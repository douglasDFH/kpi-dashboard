# Casos de Uso - KPI Dashboard Industrial

> 📚 **Documentación relacionada:**
> - [Inicio Rápido](INICIO.md) - Guía de inicio y comandos
> - [Arquitectura](ARCHITECTURE.md) - Estructura técnica detallada
> - [Plan de Acción](plan-de-accion-check.md) - Checklist de implementación

---

## 🏗️ Resumen de Arquitectura

### Stack Tecnológico
- **Backend:** Laravel 11 + MVC + Event-Driven Architecture
- **Frontend:** Blade + Alpine.js + Tailwind CSS
- **WebSockets:** Laravel Reverb + Laravel Echo
- **API:** RESTful versionada (`/api/v1/*`)
- **BD:** MySQL con UUIDs
- **Autenticación:** Laravel Sanctum (API) + Spatie Permission (Roles)

### Patrones de Diseño
- **Repository Pattern:** Abstracción de acceso a datos
- **Service Layer:** Lógica de negocio
- **Form Requests:** Validación centralizada
- **Event-Driven:** Eventos y Listeners para desacoplamiento

### Estructura de Controladores por Roles
```
app/Http/Controllers/
├── Admin/                    # Gestión completa del sistema
│   ├── DashboardController
│   ├── MaquinaController
│   ├── PlanMaquinaController
│   ├── AreaController
│   └── ReporteKpiController
├── Supervisor/               # Gestión de jornadas y mantenimiento
│   ├── DashboardController
│   ├── JornadaController
│   ├── MantenimientoController
│   └── MonitorController
└── Api/V1/Maquina/          # API para máquinas (versionada)
    ├── ProduccionController
    ├── StatusController
    └── HeartbeatController
```

---

## 📋 Casos de Uso

A continuación, se detalla el flujo de datos para los 10 casos de uso principales del sistema, basados en la arquitectura de base de datos definida.

## 1. Administrador define un plan (horario) y objetivos a una máquina

**Acción del Usuario:** El Administrador entra al panel de "Configuración de Máquinas", selecciona la "Prensa 1" y crea un nuevo plan de producción.

### Proceso del Sistema (Escritura):

- El sistema crea una nueva fila en la tabla `planes_maquina`.
- Esta fila contiene los datos de la plantilla:
  - `maquina_id`: El UUID de la "Prensa 1".
  - `nombre_plan`: "Turno Mañana - Producto ABC".
  - `objetivo_unidades`: 1500
  - `ideal_cycle_time_seconds`: 30 (para el cálculo de Rendimiento).
  - `limite_fallos_critico`: 10 (límite para parada automática de QA).
  - `activo`: true.
- Si ya existía un plan "Turno Mañana" (`activo: true`), el sistema primero lo marca como `activo: false`.

**Resultado:** La "Prensa 1" tiene un plan de trabajo listo para ser ejecutado.

---

## 2. Encargado inicia y finaliza la jornada de producción

Este caso de uso aclara que el supervisor gestiona la jornada, no la producción.

**Acción (Inicio):** El supervisor (encargado) llega a la "Prensa 1", realiza su chequeo visual de la máquina y presiona "Iniciar Jornada" en la interfaz.

### Proceso del Sistema (Escritura):

- El sistema busca el `planes_maquina` que esté `activo: true` para esa `maquina_id`.
- Crea una nueva fila en la tabla `jornadas_produccion`.
- **Snapshot (Copia):** El sistema "congela" los objetivos del plan en la nueva fila de la jornada:
  - `plan_maquina_id`: El UUID del plan consultado.
  - `maquina_id`: El UUID de la "Prensa 1".
  - `supervisor_id`: El ID del supervisor que está logueado.
  - `status`: 'running'.
  - `inicio_real`: now().
  - `objetivo_unidades_copiado`: 1500 (copiado del plan).
  - `limite_fallos_critico_copiado`: 10 (copiado del plan).

**Acción (Finalizar):** Al final del turno, el supervisor presiona "Finalizar Jornada".

### Proceso del Sistema (Actualización):

- Actualiza la fila de `jornadas_produccion` que estaba `status: 'running'`:
  - `status`: 'completed'.
  - `fin_real`: now().

**Acción Clave:** Esta actualización dispara un Job (trabajo en cola) que calcula los KPIs finales (OEE, Disponibilidad, etc.) y guarda el resultado en la tabla `resultados_kpi_jornada`.

---

## 3. Máquina registra producción (1 a 1 o por lote)

**Acción (Máquina):** La máquina (simulada por tu API) está activa y produce un lote de 10 piezas.

### Proceso del Sistema (Escritura + Actualización):

- La máquina envía una petición POST a la API de Laravel con su token (Sanctum) y un JSON: `{ "cantidad_producida": 10, "cantidad_buena": 9, "cantidad_mala": 1 }`.
- El sistema busca la `jornadas_produccion` activa (`status: 'running'`) para esa `maquina_id`.
- Crea una nueva fila en `registros_produccion` con los datos del JSON y el `jornada_id` correspondiente.
- **Agregación en Tiempo Real:** Para que el dashboard sea rápido, el sistema también actualiza la fila de `jornadas_produccion` activa (usando `increment`):
  - `total_unidades_producidas = total_unidades_producidas + 10`
  - `total_unidades_buenas = total_unidades_buenas + 9`
  - `total_unidades_malas = total_unidades_malas + 1`
- **Broadcast:** El sistema dispara un evento WebSocket (vía Reverb) con estos nuevos totales. Los dashboards de los admins se actualizan en vivo.

---

## 4. (Opcional) Máquina se detiene por límite de fallos

**Acción (Sistema):** Ocurre el Caso 3. La máquina reporta `{... "cantidad_mala": 1 }`.

### Proceso del Sistema (Verificación + Escritura):

- El sistema actualiza `jornadas_produccion` (como en el Caso 3). El `total_unidades_malas` ahora suma 10.
- El sistema compara: `total_unidades_malas (10) >= limite_fallos_critico_copiado (10)`.
- La condición es true. El sistema automáticamente:
  - Actualiza la `jornadas_produccion` activa: `status = 'stopped_critical'`.
  - Crea una nueva fila en `eventos_parada_jornada`:
    - `jornada_id`: El UUID de la jornada activa.
    - `motivo`: 'falla_critica_qa'.
    - `inicio_parada`: now().
  - Dispara un evento WebSocket. El dashboard de la máquina se pone en "Rojo (Crítico)". La máquina ya no puede enviar más `registros_produccion` hasta que se reanude (Caso 6).

---

## 5. (Opcional) Supervisor detiene por razón "x" una máquina

**Acción (Usuario):** El supervisor ve que falta materia prima y presiona "Pausar Jornada" en la interfaz.

### Proceso del Sistema (Escritura):

- El sistema actualiza la `jornadas_produccion` activa: `status = 'paused'`.
- Crea una nueva fila en `eventos_parada_jornada`:
  - `jornada_id`: El UUID de la jornada activa.
  - `motivo`: 'pausa_supervisor'.
  - `inicio_parada`: now().
  - `comentarios`: "Falta de materia prima".
- Dispara un evento WebSocket. El dashboard de la máquina se pone en "Amarillo (Pausa)".

---

## 6. (Opcional) Supervisor continúa producción (post-mantenimiento)

**Contexto:** La máquina está en `status: 'stopped_critical'` (Caso 4). Un técnico la calibra. El supervisor debe documentar esto.

**Acción (Usuario) - Paso 1:** El supervisor va a la sección "Mantenimiento" y crea una nueva fila en `registros_mantenimiento`:

- `maquina_id`: El UUID de la "Prensa 1".
- `supervisor_id`: Su propio ID.
- `jornada_id`: El UUID de la jornada actual (para vincular el evento).
- `tipo`: 'calibracion'.
- `descripcion`: "Se recalibra sensor de calidad tras parada automática".

**Acción (Usuario) - Paso 2:** El supervisor presiona "Reanudar Jornada".

### Proceso del Sistema (Actualización):

- El sistema busca el último `eventos_parada_jornada` abierto (donde `fin_parada` es null) para esa jornada.
- Actualiza esa fila: `fin_parada = now()`.
- Actualiza la `jornadas_produccion` activa: `status = 'running'`.
- Dispara un evento WebSocket. El dashboard de la máquina vuelve a "Verde (Corriendo)".

---

## 7. Administrador visualiza KPI por máquina

**Acción (Usuario):** El admin quiere ver el historial de OEE de la "Prensa 1" del último mes.

### Proceso del Sistema (Lectura):

- El sistema no calcula nada complejo en tiempo real. No hace `SUM()` sobre la tabla `registros_produccion` (sería muy lento).
- En su lugar, consulta la tabla de reportes pre-calculada:
  ```sql
  SELECT * FROM resultados_kpi_jornada 
  WHERE maquina_id = '...' 
  AND fecha_jornada BETWEEN '...' AND '...' 
  ORDER BY fecha_jornada DESC
  ```
- Esta tabla (`resultados_kpi_jornada`) fue llenada por los Jobs que se dispararon al finalizar cada jornada (Caso 2).

**Resultado:** El admin ve un gráfico de historial instantáneo.

---

## 8. Administrador visualiza KPI por área

**Acción (Usuario):** El admin quiere ver el KPI del "Área de Prensado" (que contiene 3 máquinas).

### Proceso del Sistema (Lectura + Agregación):

- El sistema consulta las máquinas de esa área:
  ```sql
  SELECT id FROM maquinas WHERE area_id = '...'
  ```
- Luego, consulta la tabla de reportes usando esos IDs y agrupa:
  ```sql
  SELECT fecha_jornada, AVG(oee_score), AVG(disponibilidad) 
  FROM resultados_kpi_jornada 
  WHERE maquina_id IN ('uuid_prensa1', 'uuid_prensa2', 'uuid_prensa3') 
  AND fecha_jornada BETWEEN '...' AND '...' 
  GROUP BY fecha_jornada
  ```

**Resultado:** El admin ve el KPI promedio de toda el área, día por día.

---

## 9. Administrador visualiza historial de cambios de planes (horarios)

**Acción (Usuario):** El admin quiere ver cómo han cambiado los objetivos de la "Prensa 1" a lo largo del tiempo.

### Proceso del Sistema (Lectura):

- El sistema consulta `planes_maquina`:
  ```sql
  SELECT * FROM planes_maquina 
  WHERE maquina_id = '...' 
  ORDER BY created_at DESC
  ```

**Resultado:** El admin ve una lista de todos los planes creados para esa máquina. Puede ver el plan `activo: true` (el actual) y todos los planes `activo: false` (los antiguos), permitiéndole comparar cómo han cambiado los `objetivo_unidades` o el `limite_fallos_critico`.

---

## 10. Admin/Encargado visualiza historial de eventos/mantenimientos

**Acción (Usuario):** El admin quiere ver todos los eventos de la "Prensa 1" del último mes.

### Proceso del Sistema (Lectura Múltiple):

El sistema necesita un historial combinado y realiza dos consultas:

**Query 1 (Mantenimientos):**
```sql
SELECT tipo, descripcion, created_at 
FROM registros_mantenimiento 
WHERE maquina_id = '...' 
AND created_at BETWEEN ...
```

**Query 2 (Paradas):**
```sql
SELECT e.motivo, e.comentarios, e.inicio_parada, e.fin_parada 
FROM eventos_parada_jornada e 
JOIN jornadas_produccion j ON e.jornada_id = j.id 
WHERE j.maquina_id = '...' 
AND e.inicio_parada BETWEEN ...
```

**Resultado:** La interfaz recibe ambos listados, los mezcla y los muestra en una sola línea de tiempo, mostrando al admin todas las paradas (automáticas y manuales) y todos los mantenimientos (preventivos, correctivos y calibraciones).

---

# Esquema de Base de Datos: KPI Dashboard Industrial (v5)

**Plataforma:** Laravel (Blade, Reverb)  
**Lógica:** La Máquina reporta todo. El Supervisor facilita.  
**IDs:** UUID para tablas de aplicación.  
**Sintaxis:** Corregida con Refs Top-Level (Requerido por dbdiagram.io)

---

## 1. NÚCLEO DE LARAVEL (ESTÁNDAR - INGLÉS)

```dbdiagram
Table users {
  id bigint [pk, increment]
  name varchar(255) [not null]
  email varchar(255) [unique, not null]
  email_verified_at timestamp [null]
  password varchar(255) [not null]
  remember_token varchar(100) [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'Usuarios (Admin, Supervisor/Encargado). Son solo Personas.'
}
```

### 2. LARAVEL SANCTUM (API - INGLÉS)

> ⚠️ **Nota:** Se omite la tabla `personal_access_tokens` en el diagrama, pero se usará en la implementación.
> - El campo `tokenable_type` será 'App\Models\Maquina'.
> - El campo `tokenable_id` será el `uuid` de la tabla `maquinas`.

### 3. SPATIE PERMISSION (ROLES - INGLÉS)

> ⚠️ **Nota:** Se omiten las tablas `roles`, `permissions`, `model_has_roles`, etc. Se usarán para `users` (Admin, Supervisor).

---

## 4. ESTRUCTURA DE LA FÁBRICA (ESPAÑOL)

```dbdiagram
// ---
// 4. ESTRUCTURA DE LA FÁBRICA (ESPAÑOL)
// ---

Table areas {
  id uuid [pk]
  nombre varchar(255) [not null, unique]
  descripcion text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
  deleted_at timestamp [null]

  Note: 'Bloques o Áreas de la fábrica'
}

Table maquinas {
  id uuid [pk]
  area_id uuid [not null]
  nombre varchar(255) [not null]
  modelo varchar(255) [null]
  status enum('running', 'stopped', 'maintenance', 'idle') [not null, default: 'idle']
  created_at timestamp [null]
  updated_at timestamp [null]
  deleted_at timestamp [null]

  Note: 'Equipos/Máquinas. Esta entidad se autentica vía Sanctum (tokenable).'
}
```

## 5. PLANIFICACIÓN (EL "HORARIO" PLANTILLA)

```dbdiagram
// ---
// 5. PLANIFICACIÓN (EL "HORARIO" PLANTILLA)
// ---

Table planes_maquina {
  id uuid [pk]
  maquina_id uuid [not null]
  nombre_plan varchar(255) [not null, Note: 'Ej: "Turno Mañana - Producto X"']
  objetivo_unidades int [not null, default: 1000]
  unidad_medida varchar(50) [not null, default: 'piezas']
  ideal_cycle_time_seconds float [not null, default: 0, Note: 'Segundos por unidad/lote (para KPI Performance)']
  limite_fallos_critico int [not null, default: 10, Note: 'Límite de fallos antes de detener (QA)']
  activo boolean [not null, default: true]
  created_at timestamp [null]
  updated_at timestamp [null]
  deleted_at timestamp [null]
  
  Indexes {
    (maquina_id, activo)
  }

  Note: 'Plantillas de configuración (el "Horario" base). Puede haber varios por máquina.'
}
```

## 6. EJECUCIÓN (LA "COPIA DEL HORARIO" O JORNADA)

```dbdiagram
// ---
// 6. EJECUCIÓN (LA "COPIA DEL HORARIO" O JORNADA)
// ---

Table jornadas_produccion {
  id uuid [pk]
  plan_maquina_id uuid [not null, Note: 'Plan del que se copió']
  maquina_id uuid [not null]
  supervisor_id bigint [not null, Note: 'Usuario que inició la jornada']
  status enum('pending', 'running', 'paused', 'completed', 'stopped_critical') [not null, default: 'pending']
  
  // Timestamps Reales
  inicio_real timestamp [null, Note: 'Timestamp real de inicio']
  fin_real timestamp [null, Note: 'Timestamp real de fin']
  
  // Snapshot (Copia) del Plan
  objetivo_unidades_copiado int [not null]
  unidad_medida_copiado varchar(50) [not null]
  limite_fallos_critico_copiado int [not null]
  
  // Datos Agregados (para dashboards rápidos)
  total_unidades_producidas int [not null, default: 0]
  total_unidades_buenas int [not null, default: 0]
  total_unidades_malas int [not null, default: 0]
  
  created_at timestamp [null]
  updated_at timestamp [null]
  
  Indexes {
    (maquina_id, status)
    (created_at)
  }

  Note: 'La "copia del horario". Representa un turno de trabajo real.'
}

Table eventos_parada_jornada {
  id uuid [pk]
  jornada_id uuid [not null]
  motivo enum('pausa_programada', 'pausa_supervisor', 'mantenimiento', 'falla_critica_qa') [not null]
  inicio_parada timestamp [not null]
  fin_parada timestamp [null]
  comentarios text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
  
  Indexes {
    (jornada_id, fin_parada)
  }

  Note: 'La "tabla adicional de todos los breaks" (paradas) de una jornada.'
}
```

## 7. REGISTROS EN VIVO (EL "REGISTRO DE PRODUCTO")

```dbdiagram
// ---
// 7. REGISTROS EN VIVO (EL "REGISTRO DE PRODUCTO")
// ---

Table registros_produccion {
  id uuid [pk]
  jornada_id uuid [not null]
  maquina_id uuid [not null]
  
  // Datos reportados por la máquina
  cantidad_producida int [not null, Note: 'Unidades en este lote/evento']
  cantidad_buena int [not null]
  cantidad_mala int [not null]
  
  // 'timestamp de creacion' es 'created_at'
  created_at timestamp [not null, default: `now()`]
  updated_at timestamp [not null, default: `now()`]
  
  Indexes {
    (jornada_id)
    (created_at)
  }
  
  Note: 'Log de la máquina (1 a 1 o por lote). Esta tabla alimenta los KPIs.'
}
```

## 8. MANTENIMIENTO (ACCIÓN DEL SUPERVISOR)

```dbdiagram
// ---
// 8. MANTENIMIENTO (ACCIÓN DEL SUPERVISOR)
// ---

Table registros_mantenimiento {
  id uuid [pk]
  maquina_id uuid [not null]
  supervisor_id bigint [not null]
  jornada_id uuid [null, Note: 'Opcional: Si ocurrió durante una jornada']
  tipo enum('preventivo', 'correctivo', 'calibracion') [not null]
  descripcion text [not null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'Única tabla que el supervisor llena manualmente.'
}
```

## 9. RESULTADOS (PARA HISTORIAL)

```dbdiagram
// ---
// 9. RESULTADOS (PARA HISTORIAL)
// ---

Table resultados_kpi_jornada {
  id uuid [pk]
  jornada_id uuid [not null, unique]
  maquina_id uuid [not null]
  fecha_jornada date [not null]

  // KPIs
  disponibilidad float [not null]
  rendimiento float [not null]
  calidad float [not null]
  oee_score float [not null, Note: 'OEE = D * R * C']

  // Tiempos (calculados para el reporte)
  tiempo_planificado_segundos int [not null]
  tiempo_paradas_programadas_segundos int [not null]
  tiempo_paradas_no_programadas_segundos int [not null]
  tiempo_operacion_real_segundos int [not null]

  created_at timestamp [null]
  updated_at timestamp [null]
  

  Indexes {
    (maquina_id, fecha_jornada)
  }

  Note: 'Tabla de reportes. Se llena con un Job al finalizar una jornada_produccion.'
}
```

## 10. RELACIONES (REQUERIDAS A NIVEL SUPERIOR - TOP-LEVEL)

```dbdiagram
// ---
// 10. RELACIONES (REQUERIDAS A NIVEL SUPERIOR - TOP-LEVEL)
// ---

// Estructura
Ref: maquinas.area_id > areas.id

// Planificación
Ref: planes_maquina.maquina_id > maquinas.id

// Ejecución
Ref: jornadas_produccion.plan_maquina_id > planes_maquina.id
Ref: jornadas_produccion.maquina_id > maquinas.id
Ref: jornadas_produccion.supervisor_id > users.id [delete: set null]

Ref: eventos_parada_jornada.jornada_id > jornadas_produccion.id [delete: cascade]

// Registros en Vivo
Ref: registros_produccion.jornada_id > jornadas_produccion.id [delete: cascade]
Ref: registros_produccion.maquina_id > maquinas.id

// Mantenimiento
Ref: registros_mantenimiento.maquina_id > maquinas.id
Ref: registros_mantenimiento.supervisor_id > users.id [delete: set null]
Ref: registros_mantenimiento.jornada_id > jornadas_produccion.id [delete: set null]

// Resultados
Ref: resultados_kpi_jornada.jornada_id > jornadas_produccion.id [delete: cascade]
Ref: resultados_kpi_jornada.maquina_id > maquinas.id
```