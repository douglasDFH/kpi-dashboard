# Caso de Uso Completo - KPI Dashboard ECOPLAST

## 📋 Índice
1. [Descripción General](#descripción-general)
2. [Actores del Sistema](#actores-del-sistema)
3. [Casos de Uso por Módulo](#casos-de-uso-por-módulo)
4. [Flujo de Trabajo Completo](#flujo-de-trabajo-completo)
5. [Escenarios de Ejemplo](#escenarios-de-ejemplo)

---

## 📖 Descripción General

**Sistema:** KPI Dashboard para manufactura de plásticos  
**Empresa:** ECOPLAST  
**Objetivo:** Monitorear y analizar KPIs de producción (OEE, disponibilidad, rendimiento, calidad) en tiempo real  
**Tecnología:** Laravel 12, Pusher (WebSockets), MySQL  

### Métricas Principales
- **OEE (Overall Equipment Effectiveness)**: Eficiencia general del equipo
- **Disponibilidad**: Tiempo operativo vs tiempo planificado
- **Rendimiento**: Producción real vs producción ideal
- **Calidad**: Unidades buenas vs unidades totales

---

## 👥 Actores del Sistema

### 1. SuperAdmin (admin@ecoplast.com)
**Permisos:** Acceso total sin restricciones
- Gestiona todos los módulos
- Crea y configura usuarios
- Asigna roles y permisos personalizados
- Accede a auditorías completas

### 2. Administrador (carlos@ecoplast.com)
**Permisos:** Según configuración personalizada
- Por defecto: Todos los permisos del rol "Administrador"
- Puede tener permisos personalizados que REEMPLAZAN los del rol
- Gestiona operaciones diarias

### 3. Supervisor de Producción
**Permisos:**
- `production.view`, `production.create`, `production.edit`
- `equipment.view`
- `reports.view`
- NO puede eliminar datos ni gestionar usuarios

### 4. Inspector de Calidad
**Permisos:**
- `quality.view`, `quality.create`, `quality.edit`, `quality.delete`
- `production.view` (solo lectura)
- `reports.view`
- NO puede gestionar equipos ni usuarios

### 5. Técnico de Mantenimiento
**Permisos:**
- `downtime.view`, `downtime.create`, `downtime.edit`
- `equipment.view`, `equipment.edit`
- NO puede eliminar equipos ni acceder a producción

### 6. Gerente de Planta
**Permisos:**
- `reports.view`, `reports.export`
- `production.view`, `quality.view`, `downtime.view`, `equipment.view`
- `audit.view`
- Solo lectura en datos operativos

### 7. Operador de Máquina
**Permisos:**
- `production.view`, `production.create`
- `equipment.view`
- Acceso mínimo para registrar producción

---

## 📦 Casos de Uso por Módulo

### 🔐 Módulo: Autenticación y Usuarios

#### CU-01: Inicio de Sesión
**Actor:** Todos  
**Precondición:** Usuario registrado en el sistema  
**Flujo Principal:**
1. Usuario accede a `/login`
2. Ingresa email y contraseña
3. Sistema valida credenciales
4. Sistema carga permisos personalizados o del rol
5. Redirige al dashboard con módulos visibles según permisos

**Postcondición:** Usuario autenticado con sesión activa

---

#### CU-02: Gestión de Usuarios (SuperAdmin)
**Actor:** SuperAdmin  
**Precondición:** Sesión activa con `users.view`  

**Flujo: Crear Usuario**
1. Click en módulo "Usuarios" desde dashboard
2. Click en "Nuevo Usuario"
3. Completar formulario:
   - Nombre, Email, Contraseña
   - Seleccionar Rol (SuperAdmin, Administrador, Supervisor, etc.)
4. **IMPORTANTE:** Seleccionar "Permisos Personalizados" (opcional)
   - Si se seleccionan: REEMPLAZAN completamente los permisos del rol
   - Si no: Usuario hereda permisos del rol asignado
5. Guardar usuario
6. Sistema registra auditoría con custom_permissions

**Flujo: Editar Permisos de Usuario Existente**
1. Buscar usuario (filtro por nombre, email, rol)
2. Click en "Editar"
3. Modificar permisos personalizados:
   - **Ejemplo:** Carlos tiene rol "Administrador" pero solo debe ver usuarios
   - Seleccionar únicamente: `users.view`
   - Al guardar: Carlos SOLO puede ver usuarios, pierde todos los demás permisos del rol
4. Sistema sincroniza tabla `user_permission`
5. Auditoría registra cambio en `custom_permissions`

**Postcondición:** Usuario creado/actualizado con permisos aplicados inmediatamente

---

### 🏭 Módulo: Equipos

#### CU-03: Registrar Nuevo Equipo
**Actor:** Administrador, Técnico Mantenimiento  
**Precondición:** Permiso `equipment.create`  

**Flujo Principal:**
1. Dashboard → "Equipos"
2. Click "Nuevo Equipo"
3. Completar datos:
   - Nombre: "Extrusora Principal A1"
   - Código: "EXT-A1"
   - Tipo: Extrusión
   - Capacidad: 500 kg/h
   - Estado: Activo
4. Guardar equipo
5. Sistema valida código único
6. Registra auditoría: `created` en `equipment`

**Postcondición:** Equipo disponible para registros de producción

---

#### CU-04: Mantenimiento de Equipo
**Actor:** Técnico Mantenimiento  
**Precondición:** Equipo con downtime programado  

**Flujo:**
1. Dashboard → "Tiempos Muertos"
2. Filtrar por equipo "EXT-A1"
3. Ver histórico de paros
4. Analizar categorías: planificado vs no planificado
5. Generar reporte de MTBF (Mean Time Between Failures)

---

### 📊 Módulo: Producción

#### CU-05: Registrar Datos de Producción
**Actor:** Supervisor Producción, Operador Máquina  
**Precondición:** `production.create`, Equipo activo  

**Flujo Principal:**
1. Dashboard → "Producción"
2. Click "Registrar Producción"
3. Formulario:
   - Equipo: Extrusora Principal A1
   - Fecha/Hora Inicio: 2025-11-10 07:00
   - Fecha/Hora Fin: 2025-11-10 15:00 (turno de 8h)
   - Unidades Producidas: 3,800 kg
   - Unidades Defectuosas: 120 kg
   - Tiempo Operativo: 450 minutos (7.5h reales)
4. Sistema calcula automáticamente:
   ```
   Disponibilidad = (450/480) × 100 = 93.75%
   Rendimiento = (3800/4000) × 100 = 95%
   Calidad = ((3800-120)/3800) × 100 = 96.84%
   OEE = 93.75% × 95% × 96.84% = 86.25%
   ```
5. Guardar registro
6. **WebSocket** dispara evento `ProductionDataUpdated`
7. Dashboard se actualiza en TIEMPO REAL para todos los usuarios conectados

**Postcondición:** KPIs actualizados, visible en dashboard

---

#### CU-06: Editar Registro de Producción (Corrección)
**Actor:** Supervisor  
**Precondición:** `production.edit`  

**Flujo:**
1. Detectar error en registro (unidades defectuosas mal capturadas)
2. Ir a Producción → Buscar registro
3. Click "Editar"
4. Modificar: Unidades Defectuosas de 120 kg a 50 kg
5. Guardar cambios
6. Sistema recalcula OEE automáticamente
7. Auditoría registra:
   ```json
   {
     "action": "updated",
     "old_values": {"defective_units": 120, "oee": 86.25},
     "new_values": {"defective_units": 50, "oee": 87.89}
   }
   ```

**Postcondición:** Datos corregidos, trazabilidad completa

---

### 🔬 Módulo: Calidad

#### CU-07: Realizar Inspección de Calidad
**Actor:** Inspector de Calidad  
**Precondición:** `quality.create`, Producción existente  

**Flujo Principal:**
1. Dashboard → "Calidad"
2. Click "Nueva Inspección"
3. Datos:
   - Equipo: Inyectora B2
   - Fecha Inspección: 2025-11-10 14:30
   - Unidades Inspeccionadas: 1,000 piezas
   - Unidades Aprobadas: 980 piezas
   - Unidades Rechazadas: 20 piezas
   - Tipo Defecto: "Rebaba excesiva"
   - Inspector: María González
   - Notas: "Ajustar presión de molde"
4. Sistema calcula:
   ```
   Tasa de Calidad = (980/1000) × 100 = 98%
   Tasa de Defectos = (20/1000) × 100 = 2%
   ```
5. Guardar inspección
6. Actualiza gráficos de tendencia de calidad

**Postcondición:** Inspección registrada, acción correctiva documentada

---

#### CU-08: Análisis de Defectos (Pareto)
**Actor:** Inspector, Gerente Planta  
**Precondición:** `quality.view`, Datos históricos  

**Flujo:**
1. Calidad → Filtrar por rango: Última semana
2. Visualizar tipos de defectos:
   - Rebaba: 45%
   - Deformación: 30%
   - Color fuera de especificación: 15%
   - Otros: 10%
3. Exportar reporte PDF con gráfico Pareto
4. Identificar acción: Enfocarse en resolver rebaba primero

---

### ⏱️ Módulo: Tiempos Muertos (Downtime)

#### CU-09: Registrar Paro No Planificado
**Actor:** Operador, Técnico Mantenimiento  
**Precondición:** `downtime.create`  

**Flujo Principal:**
1. Evento: Extrusora A1 se detiene inesperadamente
2. Operador registra inmediatamente:
   - Dashboard → "Tiempos Muertos" → "Registrar"
   - Equipo: Extrusora A1
   - Inicio: 2025-11-10 10:15
   - Categoría: **No Planificado**
   - Razón: "Falla en motor principal"
   - Descripción: "Motor sobrecalentado, requiere revisión urgente"
3. Técnico llega y soluciona problema
4. Técnico actualiza registro:
   - Fin: 2025-11-10 12:45
   - Duración: 150 minutos (calculado automáticamente)
   - Agrega notas: "Reemplazado rodamiento defectuoso"
5. Sistema actualiza:
   ```
   Disponibilidad del turno = ((480-150)/480) × 100 = 68.75%
   OEE del turno afectado recalculado
   ```

**Postcondición:** Downtime registrado, afecta KPIs del período

---

#### CU-10: Planificar Mantenimiento Preventivo
**Actor:** Técnico Mantenimiento  
**Precondición:** `downtime.create`, `equipment.edit`  

**Flujo:**
1. Revisar historial de equipos
2. Programar mantenimiento:
   - Equipo: Todas las inyectoras
   - Fecha: 2025-11-15 (fin de semana)
   - Categoría: **Planificado**
   - Razón: "Mantenimiento preventivo mensual"
   - Duración estimada: 240 minutos
3. Sistema notifica a supervisores
4. No afecta negativamente el OEE (downtime esperado)

---

### 📈 Módulo: Reportes

#### CU-11: Generar Reporte OEE Mensual
**Actor:** Gerente Planta  
**Precondición:** `reports.view`, `reports.export`  

**Flujo Principal:**
1. Dashboard → "Reportes"
2. Seleccionar tipo: "Reporte OEE"
3. Configurar:
   - Período: Octubre 2025 (01/10 - 31/10)
   - Equipos: Todas las extrusoras
   - Formato: PDF
4. Click "Generar Reporte"
5. Sistema calcula:
   - OEE promedio: 82.5%
   - Mejor equipo: EXT-A1 (89.3%)
   - Peor equipo: EXT-C3 (74.1%)
   - Gráficos de tendencia diaria
   - Top 5 causas de downtime
6. Exportar PDF con logo ECOPLAST
7. Compartir con dirección

**Postcondición:** Reporte guardado en `/storage/reports/`

---

#### CU-12: Reporte Personalizado Multi-Métrica
**Actor:** Gerente Planta  
**Precondición:** `reports.export`  

**Flujo:**
1. Reportes → "Reporte Personalizado"
2. Seleccionar múltiples métricas:
   - ✅ OEE
   - ✅ Producción (kg totales)
   - ✅ Calidad (tasa de defectos)
   - ✅ Downtime (horas perdidas)
3. Seleccionar equipos: EXT-A1, EXT-A2, INY-B1
4. Período: Última semana
5. Formato: Excel (.xlsx)
6. Generar y descargar
7. Abrir en Excel para análisis avanzado

---

### 🔍 Módulo: Auditoría

#### CU-13: Revisar Auditoría de Cambios Críticos
**Actor:** SuperAdmin, Gerente Planta  
**Precondición:** `audit.view`  

**Flujo:**
1. Dashboard → "Auditoría"
2. Filtros:
   - Usuario: Carlos (Administrador)
   - Acción: `updated`
   - Modelo: `App\Models\User`
   - Fecha: Última semana
3. Resultado: Ver cambio de permisos
   ```json
   {
     "user": "SuperAdmin",
     "action": "updated",
     "model": "User (id: 2 - Carlos)",
     "old_values": {
       "role_id": 2,
       "custom_permissions": [1,2,3,4,5,6,7,...]
     },
     "new_values": {
       "role_id": 2,
       "custom_permissions": [16] // Solo users.view
     },
     "timestamp": "2025-11-10 15:30:45"
   }
   ```
4. Validar que cambio fue intencional
5. Exportar log para compliance

**Postcondición:** Trazabilidad completa de cambios

---

## 🔄 Flujo de Trabajo Completo

### Escenario: Día Operativo Completo en ECOPLAST

#### 📅 Turno Matutino (07:00 - 15:00)

**07:00 - Inicio de Turno**
1. **Operador Juan** (login: juan@ecoplast.com)
   - Inicia sesión (permisos: `production.view`, `production.create`)
   - Dashboard muestra solo módulos permitidos: Producción, Equipos (vista)
   - Verifica estado de equipos: Todos activos ✅

2. **Registro de Producción Inicial**
   - Equipo: Extrusora A1
   - Turno: Matutino
   - Objetivo: 4,000 kg de polietileno

**10:15 - Incidente: Paro Inesperado**
3. **Alarma:** Extrusora A1 se detiene
   - Juan registra downtime inmediatamente:
     - Categoría: No Planificado
     - Razón: "Motor sobrecalentado"
   - Llama a mantenimiento

4. **Técnico Pedro** (login: pedro@ecoplast.com)
   - Recibe notificación (permiso: `downtime.edit`)
   - Accede al registro de downtime
   - Revisa historial del equipo en módulo Equipos
   - Diagnostica problema: Rodamiento dañado

**12:45 - Resolución**
5. Pedro cierra registro de downtime:
   - Fin: 12:45
   - Duración: 150 minutos
   - Notas: "Rodamiento reemplazado, equipo operativo"
   - Sistema recalcula disponibilidad: 68.75%

6. Juan reanuda producción

**14:30 - Inspección de Calidad**
7. **Inspectora María** (login: maria@ecoplast.com)
   - Toma muestra de producción matutina
   - Inspecciona 500 piezas
   - Encuentra 15 defectuosas (rebaba)
   - Registra en módulo Calidad:
     - Tasa calidad: 97%
     - Tipo defecto: Rebaba
     - Acción: Ajustar temperatura

**15:00 - Cierre de Turno**
8. Juan registra producción final:
   - Unidades producidas: 3,200 kg (por downtime)
   - Defectuosas: 80 kg
   - Sistema calcula OEE: 78.5% (afectado por paro)
   - **WebSocket** actualiza dashboard en tiempo real

---

#### 📊 Turno Vespertino (15:00 - 23:00)

**15:30 - Supervisor Revisa Reportes**
9. **Supervisor Carlos** (login: carlos@ecoplast.com)
   - Dashboard muestra gráficos actualizados
   - OEE del turno matutino: 78.5% (debajo del objetivo 85%)
   - Identifica causa: Downtime de 150 min
   - Decide: Programar mantenimiento preventivo

10. Carlos accede a Auditoría:
    - Revisa cambios del día
    - Verifica registro de downtime de Pedro
    - Confirma reparación realizada

**16:00 - Gerente Genera Reporte**
11. **Gerente Laura** (login: laura@ecoplast.com)
    - Permisos: Solo lectura + exportación
    - Genera reporte semanal:
      - OEE promedio: 84.2%
      - Tendencia: Mejora del 2% vs semana anterior
      - Exporta PDF para junta directiva

**20:00 - Turno Nocturno Sin Incidentes**
12. Operador registra producción continua:
    - 4,100 kg producidos
    - 30 kg defectuosos
    - OEE: 94.3% ✅ (excelente)

---

#### 🌅 Día Siguiente

**08:00 - Reunión de Mejora Continua**
13. **SuperAdmin** presenta análisis:
    - Dashboard proyectado en pantalla (actualización en vivo)
    - Gráficos muestran:
      - OEE semanal por equipo
      - Top causas de downtime: Fallas mecánicas (40%)
      - Calidad estable: 97.5% promedio
    - Decisión: Implementar mantenimiento predictivo

14. SuperAdmin crea nuevo usuario:
    - **Ingeniero IoT** con permisos personalizados:
      - `equipment.view`, `production.view`, `downtime.view`
      - `reports.view`, `reports.export`
      - NO puede modificar datos (solo análisis)

---

## 🎯 Escenarios de Ejemplo con Permisos

### Escenario A: Usuario Restringido

**Usuario:** Carlos (Administrador con permisos personalizados)  
**Configuración:**
- Rol: Administrador (17 permisos totales)
- Permisos personalizados: Solo `users.view`

**Experiencia:**
1. Dashboard muestra SOLO botón "Usuarios"
2. Al entrar a Usuarios:
   - ✅ Ve lista de usuarios
   - ❌ NO ve botón "Nuevo Usuario"
   - ❌ NO ve botones Editar/Eliminar
3. Intenta acceder directo a `/equipment`:
   - ❌ Error 403: "No tienes permiso para ver equipos"
4. Intenta `/users/2/edit`:
   - ❌ Error 403: "No tienes permiso para editar usuarios"

**Resultado:** Seguridad de dos niveles (UI + Backend) funcionando

---

### Escenario B: Supervisor de Producción

**Usuario:** Juan Supervisor  
**Permisos:**
- `production.view`, `production.create`, `production.edit`
- `equipment.view`
- `downtime.view`
- `reports.view`

**Experiencia:**
1. Dashboard muestra: Producción, Equipos, Tiempos Muertos, Reportes
2. En Producción:
   - ✅ Ve "Registrar Producción"
   - ✅ Puede editar registros propios
   - ❌ NO ve botón "Eliminar" (no tiene `production.delete`)
3. En Equipos:
   - ✅ Ve lista de equipos
   - ❌ NO ve "Nuevo Equipo" ni botones editar
4. En Reportes:
   - ✅ Genera reportes OEE
   - ❌ NO ve botón "Exportar" (no tiene `reports.export`)

---

### Escenario C: Inspector de Calidad Total

**Usuario:** María Inspector  
**Permisos:**
- `quality.*` (view, create, edit, delete)
- `production.view`
- `reports.view`, `reports.export`

**Experiencia:**
1. Dashboard: Calidad, Producción (solo lectura), Reportes
2. En Calidad:
   - ✅ Control total: Crear, editar, eliminar inspecciones
3. En Producción:
   - ✅ Ve datos de producción
   - ❌ NO puede registrar ni editar
4. Reportes:
   - ✅ Genera reportes de calidad
   - ✅ Exporta Excel con análisis Pareto

---

## 📱 Características en Tiempo Real

### WebSocket con Pusher

**Evento:** Nueva producción registrada  
**Flujo:**
1. Operador guarda registro de producción
2. Backend dispara: `ProductionDataUpdated::dispatch($productionData)`
3. Pusher broadcast a canal: `kpi-channel`
4. Frontend escucha evento: `production-data-updated`
5. JavaScript actualiza:
   - Gráfico de OEE (sin refrescar página)
   - Tabla de producción reciente
   - Badge de notificación
6. Todos los usuarios conectados ven actualización simultánea

**Beneficio:** Decisiones basadas en datos frescos, colaboración en tiempo real

---

## 🔒 Sistema de Permisos - Reglas Clave

### Regla 1: Override Completo
```php
// Si usuario tiene custom_permissions, SOLO usa esos
if ($user->customPermissions()->exists()) {
    return $user->customPermissions()->where('name', $permission)->exists();
}
// Si NO, usa permisos del rol
return $user->role->hasPermission($permission);
```

### Regla 2: Dos Niveles de Protección
1. **Vista (Blade):** Oculta botones
   ```blade
   @if(auth()->user()->hasPermission('production.create'))
       <button>Registrar Producción</button>
   @endif
   ```

2. **Controlador:** Bloquea acceso directo
   ```php
   public function create() {
       $this->authorizePermission('production.create', 'No tienes permiso...');
       // ...
   }
   ```

### Regla 3: Auditoría Automática
Todos los cambios registran:
- Usuario que realizó acción
- Modelo afectado (User, Equipment, ProductionData, etc.)
- Valores antiguos y nuevos
- Timestamp preciso
- IP y user agent

---

## 📊 Métricas del Sistema

### Performance Esperado
- **Tiempo de cálculo OEE:** < 50ms
- **Actualización WebSocket:** < 200ms
- **Carga de dashboard:** < 1s
- **Generación reporte PDF:** < 3s
- **Concurrencia:** Hasta 50 usuarios simultáneos

### Datos de Producción
- **Equipos promedio:** 15-20 máquinas
- **Registros diarios:** ~150-200 (producción + calidad + downtime)
- **Retención histórica:** 2 años
- **Auditoría:** Permanente (compliance)

---

## 🚀 Próximos Pasos (Roadmap)

1. **Notificaciones Push:** Alertas cuando OEE < 80%
2. **Dashboard móvil:** PWA para operadores
3. **Machine Learning:** Predicción de fallas (downtime)
4. **Integración IoT:** Lectura automática de sensores
5. **Multi-planta:** Soporte para múltiples ubicaciones
6. **API REST:** Integración con ERP/MES externo

---

## 📞 Contacto y Soporte

**Desarrollador:** douglasDFH  
**Repositorio:** kpi-dashboard  
**Versión:** 1.0.0  
**Fecha:** Noviembre 2025

---

**Fin del Documento** 📄
