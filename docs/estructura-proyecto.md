# Estructura del Proyecto KPI Dashboard - Metalúrgica Precision S.A.

## 📊 Estado Actual del Proyecto

### ✅ Módulos Completados

#### 1. **Dashboard Principal**
- **Archivo:** `resources/views/dashboard.blade.php`
- **Controlador:** `app/Http/Controllers/DashboardController.php`
- **Funcionalidad:**
  - Visualización de KPIs en tiempo real
  - Gráficos de OEE (Disponibilidad, Rendimiento, Calidad)
  - Selector de equipos
  - Métricas adicionales (producción total, unidades defectuosas, downtime)

#### 2. **Gestión de Equipos**
- **Modelo:** `app/Models/Equipment.php`
- **Controlador:** `app/Http/Controllers/EquipmentController.php`
- **Vistas:** `resources/views/equipment/`
  - `index.blade.php` - Listado de equipos
  - `create.blade.php` - Crear nuevo equipo
  - `edit.blade.php` - Editar equipo
- **Funcionalidad:**
  - CRUD completo de equipos
  - Tipos: Prensa, Torno, Fresadora, Línea de Ensamblaje
  - Estados: Activo/Inactivo
  - Relaciones con datos de producción, calidad y downtime

#### 3. **Datos de Producción**
- **Modelo:** `app/Models/ProductionData.php`
- **Controlador:** `app/Http/Controllers/ProductionDataController.php`
- **Vistas:** `resources/views/production/`
  - `index.blade.php` - Listado con filtros
  - `create.blade.php` - Registrar producción
  - `edit.blade.php` - Editar registro
- **Funcionalidad:**
  - Registro de producción planificada vs real
  - Cálculo automático de unidades defectuosas
  - Eficiencia y tasa de calidad
  - Filtros por equipo y rango de fechas

#### 4. **Tiempos Muertos (Downtime)**
- **Modelo:** `app/Models/DowntimeData.php`
- **Controlador:** `app/Http/Controllers/DowntimeDataController.php`
- **Vistas:** `resources/views/downtime/`
  - `index.blade.php` - Listado de paros
  - `create.blade.php` - Registrar tiempo muerto
  - `edit.blade.php` - Editar registro
- **Funcionalidad:**
  - Categorías: Planificado / No planificado
  - Razones: Mantenimiento, Fallas, Operación, Otros
  - Cálculo automático de duración
  - Impacto directo en disponibilidad

#### 5. **Servicio de KPI**
- **Servicio:** `app/Services/KpiService.php`
- **Funcionalidad:**
  - Cálculo de OEE (Overall Equipment Effectiveness)
  - Cálculo de Disponibilidad (Availability)
  - Cálculo de Rendimiento (Performance)
  - Cálculo de Calidad (Quality)
  - Métricas agregadas por equipo

#### 6. **API REST**
- **Rutas:** `routes/api.php`
- **Controladores API:**
  - `Api/EquipmentController.php`
  - `Api/KpiController.php`
  - `Api/ProductionDataController.php`
- **Endpoints:**
  - `/api/equipment` - Gestión de equipos
  - `/api/kpi/{equipmentId}` - Obtener KPIs
  - `/api/production-data` - Datos de producción

---

## ⏳ Módulos Pendientes

### 1. **Inspecciones de Calidad** (PENDIENTE)
- **Modelo existente:** `app/Models/QualityData.php` ✅
- **Tabla existente:** `quality_data` ✅
- **Controlador:** ❌ Falta crear `QualityDataController.php`
- **Vistas:** ❌ Falta crear carpeta `resources/views/quality/`

**Campos disponibles en la tabla:**
- `equipment_id` - Equipo inspeccionado
- `total_inspected` - Total de unidades inspeccionadas
- `approved_units` - Unidades aprobadas
- `rejected_units` - Unidades rechazadas
- `defect_type` - Tipo de defecto
- `notes` - Notas adicionales
- `inspection_date` - Fecha de inspección

**Funcionalidades a implementar:**
- ✅ CRUD de inspecciones de calidad
- ✅ Registro de defectos por tipo
- ✅ Gráficos de tendencias de calidad
- ✅ Filtros por equipo, fecha, tipo de defecto
- ✅ Estadísticas de aprobación/rechazo

---

### 2. **Reportes y Análisis** (PENDIENTE)
**Funcionalidades a implementar:**
- ✅ Reporte de OEE por equipo
- ✅ Reporte de producción consolidada
- ✅ Análisis de tiempos muertos
- ✅ Tendencias de calidad
- ✅ Comparativas entre equipos
- ✅ Exportación a PDF/Excel
- ✅ Gráficos avanzados (Chart.js)

**Archivos a crear:**
- `app/Http/Controllers/ReportController.php`
- `resources/views/reports/`
  - `oee.blade.php`
  - `production.blade.php`
  - `downtime.blade.php`
  - `quality.blade.php`
  - `comparative.blade.php`

---

### 3. **Gestión de Usuarios y Roles** (PENDIENTE)
**Modelo existente:** `app/Models/User.php` ✅

**Funcionalidades a implementar:**
- ✅ Autenticación de usuarios
- ✅ Gestión de roles (Admin, Supervisor, Operador, etc.)
- ✅ Permisos por módulo
- ✅ CRUD de usuarios
- ✅ Auditoría de acciones

**Roles sugeridos:**
1. **Administrador del Sistema** - Acceso total
2. **Gerente de Planta** - Acceso a reportes y análisis
3. **Supervisor de Producción** - Gestión de producción y equipos
4. **Operador** - Solo registro de datos
5. **Ingeniero de Procesos** - Análisis y optimización
6. **Técnico de Mantenimiento** - Gestión de downtime
7. **Inspector de Calidad** - Gestión de inspecciones

**Archivos a crear:**
- `app/Models/Role.php`
- `app/Models/Permission.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/RoleController.php`
- `database/migrations/create_roles_and_permissions_tables.php`
- `resources/views/users/`
- `resources/views/roles/`

---

## 🗄️ Base de Datos

### Tablas Existentes:
1. ✅ `equipment` - Equipos industriales
2. ✅ `production_data` - Datos de producción
3. ✅ `quality_data` - Datos de calidad (sin usar aún)
4. ✅ `downtime_data` - Tiempos muertos
5. ✅ `users` - Usuarios del sistema
6. ✅ `cache` - Caché de Laravel
7. ✅ `jobs` - Trabajos en cola
8. ✅ `personal_access_tokens` - Tokens de API

### Tablas a Crear:
1. ❌ `roles` - Roles de usuario
2. ❌ `permissions` - Permisos
3. ❌ `role_user` - Relación muchos a muchos
4. ❌ `permission_role` - Relación muchos a muchos
5. ❌ `activity_log` - Auditoría (opcional)

---

## 🎨 Vistas Existentes

```
resources/views/
├── auth/ (eliminar - no se usa)
├── dashboard.blade.php ✅
├── equipment/ ✅
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── production/ ✅
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── downtime/ ✅
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── welcome.blade.php (Laravel default)
```

---

## 🔧 Servicios y Componentes

### Servicios Existentes:
- ✅ `KpiService.php` - Cálculos de KPIs

### Eventos Existentes:
- ✅ `KpiUpdated.php` - Evento de actualización de KPI
- ✅ `ProductionDataUpdated.php` - Evento de actualización de producción

### Características Técnicas:
- Laravel 12
- Tailwind CSS 4.0
- Chart.js para gráficos
- Pusher para WebSockets (tiempo real)
- Axios para peticiones AJAX
- MySQL como base de datos

---

## 📈 Progreso del Proyecto

**Módulos Completados:** 5/8 (62.5%)
- ✅ Dashboard Principal
- ✅ Gestión de Equipos
- ✅ Datos de Producción
- ✅ Tiempos Muertos
- ✅ API REST

**Módulos Pendientes:** 3/8 (37.5%)
- ⏳ Inspecciones de Calidad
- ⏳ Reportes y Análisis
- ⏳ Gestión de Usuarios y Roles

---

## 🎯 Próximos Pasos Recomendados

### Prioridad 1: Inspecciones de Calidad
- Ya tiene modelo y tabla creados
- Solo falta crear controlador y vistas
- Integración con KpiService

### Prioridad 2: Reportes y Análisis
- Aprovecha todos los datos existentes
- Genera valor inmediato para gerencia
- Requiere lógica de agregación

### Prioridad 3: Gestión de Usuarios
- Fundamental para producción
- Requiere creación de tablas nuevas
- Implementar con paquete como Spatie Permission

---

**Generado:** $(date)
**Versión:** 1.0
