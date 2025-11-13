# ✅ Plan de Acción - KPI Dashboard Industrial

## 📋 Checklist de Implementación

> **Estado del Proyecto:** 🟡 En Desarrollo  
> **Base de Datos:** ✅ Definida  
> **Casos de Uso:** ✅ Documentados  
> **Arquitectura:** ✅ Definida

---

## 🎯 Fase 0: Preparación Inicial

### ✅ Documentación
- [x] Definir casos de uso → `casos de usos.md`
- [x] Definir arquitectura → `ARCHITECTURE.md`
- [x] Crear guía de inicio → `INICIO.md`
- [x] Crear plan de acción → `plan-de-accion-check.md`
- [x] Crear guía de limpieza → `LIMPIEZA-PLANTILLA.md`

### 🔲 Configuración del Entorno

> 🧹 **Guía Completa:** Ver [LIMPIEZA-PLANTILLA.md](LIMPIEZA-PLANTILLA.md) para instrucciones detalladas

#### Paso 1: Purgar Dependencias Innecesarias
```bash
# Remover Pusher (usaremos Laravel Reverb)
composer remove pusher/pusher-php-server
npm uninstall pusher-js

# Remover Laravel Echo (reinstalaremos después con Reverb)
npm uninstall laravel-echo

# Opcional: Remover DomPDF si no lo usarás
composer remove barryvdh/laravel-dompdf

# Opcional: Remover Laravel Sail si no usas Docker
composer remove --dev laravel/sail
```

#### Paso 2: Instalación Base
- [ ] Instalar dependencias PHP restantes (`composer install`)
- [ ] Instalar dependencias Node restantes (`npm install`)
- [ ] Configurar archivo `.env`
- [ ] Configurar base de datos MySQL
- [ ] Generar key de aplicación (`php artisan key:generate`)

### 🔲 Instalación de Paquetes Necesarios

#### Autenticación y Permisos
```bash
# Spatie Permission (Roles y Permisos)
composer require spatie/laravel-permission

# Publicar migraciones
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

#### WebSockets (Laravel Reverb)
```bash
# Instalar Laravel Reverb
php artisan install:broadcasting

# Esto instalará automáticamente:
# - laravel/reverb (Composer)
# - laravel-echo (npm)
# - pusher-js (npm) <- Se necesita para el protocolo

# Verificar que se agregó a composer.json y package.json
```

#### Frontend
```bash
# Alpine.js (componentes interactivos)
npm install alpinejs

# Chart.js (gráficos) - Elegir UNO
npm install chart.js
# O
npm install apexcharts

# Opcional: Heroicons (iconos de Tailwind)
npm install @heroicons/vue
```

#### Desarrollo
```bash
# Laravel Pint (code formatting) - ya debería estar en dev
composer require --dev laravel/pint

# Laravel Debugbar (debug en desarrollo)
composer require --dev barryvdh/laravel-debugbar
```

---

## 📦 Fase 1: Base de Datos y Modelos

### 🔲 1.1 Migraciones

#### Prioridad Alta
- [ ] `create_areas_table.php` → Áreas de la fábrica
- [ ] `create_maquinas_table.php` → Máquinas/Equipos
- [ ] `create_planes_maquina_table.php` → Planes de producción
- [ ] `create_jornadas_produccion_table.php` → Jornadas de trabajo
- [ ] `create_eventos_parada_jornada_table.php` → Paradas/Pausas
- [ ] `create_registros_produccion_table.php` → Log de producción
- [ ] `create_registros_mantenimiento_table.php` → Mantenimientos
- [ ] `create_resultados_kpi_jornada_table.php` → KPIs pre-calculados

#### Comandos
```bash
# Crear migraciones faltantes
php artisan make:migration create_areas_table
php artisan make:migration create_maquinas_table
# ... (resto de migraciones)

# Ejecutar migraciones
php artisan migrate
```

### 🔲 1.2 Modelos Eloquent

- [ ] `app/Models/Area.php`
- [ ] `app/Models/Maquina.php`
- [ ] `app/Models/PlanMaquina.php`
- [ ] `app/Models/JornadaProduccion.php`
- [ ] `app/Models/EventoParadaJornada.php`
- [ ] `app/Models/RegistroProduccion.php`
- [ ] `app/Models/RegistroMantenimiento.php`
- [ ] `app/Models/ResultadoKpiJornada.php`

#### Características de Modelos
- [ ] Usar `HasUuids` trait
- [ ] Definir `$fillable` o `$guarded`
- [ ] Configurar relaciones (`belongsTo`, `hasMany`)
- [ ] Agregar casts para fechas y tipos
- [ ] Agregar accessors/mutators si es necesario

### 🔲 1.3 Seeders

- [ ] `RoleSeeder.php` → Crear roles (admin, supervisor)
- [ ] `UserSeeder.php` → Usuarios de prueba
- [ ] `AreaSeeder.php` → Áreas ejemplo (Prensado, Ensamblaje, etc.)
- [ ] `MaquinaSeeder.php` → Máquinas ejemplo + tokens Sanctum
- [ ] `PlanMaquinaSeeder.php` → Planes activos para máquinas

```bash
# Crear seeders
php artisan make:seeder RoleSeeder
# ...

# Ejecutar seeders
php artisan db:seed
```

---

## 🔐 Fase 2: Autenticación y Autorización

### 🔲 2.1 Sistema de Autenticación

**Opción 1: Laravel Breeze (Recomendado - Simple)**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

**Opción 2: Laravel Jetstream (Avanzado)**
```bash
composer require laravel/jetstream
php artisan jetstream:install livewire
npm install && npm run build
```

- [ ] Instalar paquete de autenticación
- [ ] Ejecutar migraciones
- [ ] Personalizar vistas con Tailwind
- [ ] Configurar redirecciones por rol

### 🔲 2.2 Roles y Permisos (Spatie)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

- [ ] Instalar Spatie Permission
- [ ] Crear `RoleSeeder.php`
- [ ] Definir permisos:
  - `view-dashboard`
  - `manage-maquinas`
  - `manage-planes`
  - `manage-jornadas`
  - `register-mantenimiento`
  - `view-all-reportes`
  - `manage-users`
- [ ] Asignar roles en `UserSeeder`

### 🔲 2.3 Middleware de Roles

- [ ] Crear `EnsureUserHasRole.php`
- [ ] Registrar en `Kernel.php`
- [ ] Aplicar en grupos de rutas

```php
// app/Http/Middleware/EnsureUserHasRole.php
public function handle($request, Closure $next, string $role)
{
    if (!$request->user()->hasRole($role)) {
        abort(403);
    }
    return $next($request);
}
```

### 🔲 2.4 Sanctum para API (Máquinas)

```bash
php artisan install:api
```

- [ ] Instalar Laravel Sanctum
- [ ] Configurar `HasApiTokens` en modelo `Maquina`
- [ ] Crear comando/seeder para generar tokens
- [ ] Middleware `auth:sanctum` en rutas API

---

## 🏗️ Fase 3: Arquitectura (Repositories + Services)

### 🔲 3.1 Repositories

#### Crear Interfaces
- [ ] `app/Repositories/Contracts/JornadaProduccionRepositoryInterface.php`
- [ ] `app/Repositories/Contracts/RegistroProduccionRepositoryInterface.php`
- [ ] `app/Repositories/Contracts/MaquinaRepositoryInterface.php`
- [ ] `app/Repositories/Contracts/PlanMaquinaRepositoryInterface.php`
- [ ] `app/Repositories/Contracts/ResultadoKpiRepositoryInterface.php`

#### Crear Implementaciones
- [ ] `app/Repositories/Eloquent/JornadaProduccionRepository.php`
- [ ] `app/Repositories/Eloquent/RegistroProduccionRepository.php`
- [ ] `app/Repositories/Eloquent/MaquinaRepository.php`
- [ ] `app/Repositories/Eloquent/PlanMaquinaRepository.php`
- [ ] `app/Repositories/Eloquent/ResultadoKpiRepository.php`

#### Service Provider
- [ ] `app/Providers/RepositoryServiceProvider.php`
- [ ] Registrar bindings en `boot()`
- [ ] Agregar provider a `config/app.php` (si Laravel < 11)

```php
// RepositoryServiceProvider.php
public function register()
{
    $this->app->bind(
        JornadaProduccionRepositoryInterface::class,
        JornadaProduccionRepository::class
    );
    // ... más bindings
}
```

### 🔲 3.2 Services

- [ ] `app/Services/JornadaService.php`
  - `iniciarJornada()`
  - `finalizarJornada()`
  - `pausarJornada()`
  - `reanudarJornada()`
- [ ] `app/Services/ProduccionService.php`
  - `registrarProduccion()`
  - `verificarLimiteFallos()`
- [ ] `app/Services/KpiService.php`
  - `calcularOEE()`
  - `calcularDisponibilidad()`
  - `calcularRendimiento()`
  - `calcularCalidad()`
- [ ] `app/Services/MantenimientoService.php`
  - `registrarMantenimiento()`
- [ ] `app/Services/EmuladorService.php`
  - `simularProduccion()`

---

## 📝 Fase 4: Form Requests (Validación)

### 🔲 4.1 Admin Requests

- [ ] `app/Http/Requests/Admin/StoreMaquinaRequest.php`
- [ ] `app/Http/Requests/Admin/UpdateMaquinaRequest.php`
- [ ] `app/Http/Requests/Admin/StorePlanMaquinaRequest.php`
- [ ] `app/Http/Requests/Admin/UpdatePlanMaquinaRequest.php`
- [ ] `app/Http/Requests/Admin/StoreAreaRequest.php`

```bash
php artisan make:request Admin/StoreMaquinaRequest
```

### 🔲 4.2 Supervisor Requests

- [ ] `app/Http/Requests/Supervisor/IniciarJornadaRequest.php`
- [ ] `app/Http/Requests/Supervisor/FinalizarJornadaRequest.php`
- [ ] `app/Http/Requests/Supervisor/PausarJornadaRequest.php`
- [ ] `app/Http/Requests/Supervisor/RegistrarMantenimientoRequest.php`

### 🔲 4.3 API Requests

- [ ] `app/Http/Requests/Api/V1/RegistrarProduccionRequest.php`
- [ ] `app/Http/Requests/Api/V1/ActualizarStatusRequest.php`

```php
// Ejemplo: RegistrarProduccionRequest.php
public function authorize(): bool
{
    return $this->user()->tokenCan('maquina');
}

public function rules(): array
{
    return [
        'cantidad_producida' => 'required|integer|min:1',
        'cantidad_buena' => 'required|integer|min:0',
        'cantidad_mala' => 'required|integer|min:0',
    ];
}
```

---

## 🎮 Fase 5: Controladores

### 🔲 5.1 Admin Controllers

- [ ] `app/Http/Controllers/Admin/DashboardController.php`
- [ ] `app/Http/Controllers/Admin/MaquinaController.php` (CRUD)
- [ ] `app/Http/Controllers/Admin/PlanMaquinaController.php` (CRUD)
- [ ] `app/Http/Controllers/Admin/AreaController.php` (CRUD)
- [ ] `app/Http/Controllers/Admin/ReporteKpiController.php`
- [ ] `app/Http/Controllers/Admin/UsuarioController.php`

```bash
php artisan make:controller Admin/MaquinaController --resource
```

### 🔲 5.2 Supervisor Controllers

- [ ] `app/Http/Controllers/Supervisor/DashboardController.php`
- [ ] `app/Http/Controllers/Supervisor/JornadaController.php`
- [ ] `app/Http/Controllers/Supervisor/MantenimientoController.php`
- [ ] `app/Http/Controllers/Supervisor/MonitorController.php`

### 🔲 5.3 API Controllers (Máquinas)

- [ ] `app/Http/Controllers/Api/V1/Maquina/ProduccionController.php`
- [ ] `app/Http/Controllers/Api/V1/Maquina/StatusController.php`
- [ ] `app/Http/Controllers/Api/V1/Maquina/HeartbeatController.php`

```bash
php artisan make:controller Api/V1/Maquina/ProduccionController --api
```

### 🔲 5.4 Emulador Controller

- [ ] `app/Http/Controllers/EmuladorController.php`

---

## 🛣️ Fase 6: Rutas

### 🔲 6.1 Rutas Web (`routes/web.php`)

```php
// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas Admin (autenticadas + rol)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('maquinas', MaquinaController::class);
    Route::resource('planes', PlanMaquinaController::class);
    Route::resource('areas', AreaController::class);
    Route::get('/reportes/maquina/{id}', [ReporteKpiController::class, 'maquina'])->name('reportes.maquina');
    Route::get('/reportes/area/{id}', [ReporteKpiController::class, 'area'])->name('reportes.area');
});

// Rutas Supervisor
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('jornadas', JornadaController::class)->only(['index', 'store', 'update']);
    Route::post('/jornadas/{id}/pausar', [JornadaController::class, 'pausar'])->name('jornadas.pausar');
    Route::post('/jornadas/{id}/reanudar', [JornadaController::class, 'reanudar'])->name('jornadas.reanudar');
    Route::resource('mantenimientos', MantenimientoController::class)->only(['create', 'store']);
});

// Emulador (solo en desarrollo)
Route::get('/emulator', [EmuladorController::class, 'index'])->name('emulator.index');
Route::post('/emulator/produccion', [EmuladorController::class, 'produccion'])->name('emulator.produccion');
```

### 🔲 6.2 Rutas API (`routes/api.php`)

```php
Route::prefix('v1')->group(function () {
    
    Route::middleware(['auth:sanctum', 'ability:maquina'])->group(function () {
        
        Route::prefix('maquina')->name('api.v1.maquina.')->group(function () {
            Route::post('/produccion', [ProduccionController::class, 'store'])
                ->name('produccion.store');
            Route::put('/status', [StatusController::class, 'update'])
                ->name('status.update');
            Route::post('/heartbeat', [HeartbeatController::class, 'ping'])
                ->name('heartbeat');
        });
    });
});
```

### 🔲 6.3 Canales WebSocket (`routes/channels.php`)

```php
// Canal privado por máquina
Broadcast::channel('maquina.{maquinaId}', function ($user, $maquinaId) {
    // Verificar que el usuario tenga acceso a esta máquina
    return $user->hasRole('admin') || $user->canAccessMaquina($maquinaId);
});

// Canal de área (para supervisores)
Broadcast::channel('area.{areaId}', function ($user, $areaId) {
    return $user->hasRole('admin') || $user->area_id == $areaId;
});
```

---

## 🎨 Fase 7: Vistas (Blade + Tailwind CSS)

### 🔲 7.1 Layouts

- [ ] `resources/views/layouts/app.blade.php` (Base)
- [ ] `resources/views/layouts/admin.blade.php` (Sidebar admin)
- [ ] `resources/views/layouts/supervisor.blade.php` (Sidebar supervisor)
- [ ] `resources/views/layouts/guest.blade.php` (Login/Register)

### 🔲 7.2 Componentes Blade Reutilizables

- [ ] `resources/views/components/kpi-card.blade.php` → Tarjeta de KPI
- [ ] `resources/views/components/maquina-status.blade.php` → Estado de máquina
- [ ] `resources/views/components/chart-oee.blade.php` → Gráfico OEE
- [ ] `resources/views/components/timeline-eventos.blade.php` → Línea de tiempo
- [ ] `resources/views/components/tabla-produccion.blade.php` → Tabla de registros
- [ ] `resources/views/components/alert.blade.php` → Alertas/Notificaciones
- [ ] `resources/views/components/modal.blade.php` → Modales

```bash
php artisan make:component KpiCard
```

### 🔲 7.3 Vistas Admin

- [ ] `resources/views/admin/dashboard.blade.php`
- [ ] `resources/views/admin/maquinas/index.blade.php`
- [ ] `resources/views/admin/maquinas/create.blade.php`
- [ ] `resources/views/admin/maquinas/edit.blade.php`
- [ ] `resources/views/admin/planes/index.blade.php`
- [ ] `resources/views/admin/planes/create.blade.php`
- [ ] `resources/views/admin/planes/edit.blade.php`
- [ ] `resources/views/admin/reportes/kpi-maquina.blade.php`
- [ ] `resources/views/admin/reportes/kpi-area.blade.php`

### 🔲 7.4 Vistas Supervisor

- [ ] `resources/views/supervisor/dashboard.blade.php`
- [ ] `resources/views/supervisor/jornadas/index.blade.php`
- [ ] `resources/views/supervisor/jornadas/monitor.blade.php`
- [ ] `resources/views/supervisor/mantenimiento/create.blade.php`

### 🔲 7.5 Emulador

- [ ] `resources/views/emulator/index.blade.php`

---

## 🔥 Fase 8: Eventos y WebSockets

### 🔲 8.1 Configurar Laravel Reverb

```bash
php artisan install:broadcasting
```

- [ ] Configurar `.env` con variables de Reverb
- [ ] Iniciar servidor Reverb: `php artisan reverb:start`

### 🔲 8.2 Crear Eventos

- [ ] `app/Events/JornadaIniciada.php` (implements ShouldBroadcast)
- [ ] `app/Events/JornadaFinalizada.php`
- [ ] `app/Events/ProduccionRegistrada.php`
- [ ] `app/Events/MaquinaDetenidaCritica.php`
- [ ] `app/Events/KpisActualizados.php`

```bash
php artisan make:event ProduccionRegistrada
```

### 🔲 8.3 Crear Listeners

- [ ] `app/Listeners/CalcularKpisJornada.php`
- [ ] `app/Listeners/NotificarParadaCritica.php`
- [ ] `app/Listeners/BroadcastKpisEnTiempoReal.php`

```bash
php artisan make:listener CalcularKpisJornada --event=ProduccionRegistrada
```

### 🔲 8.4 Registrar Eventos (`EventServiceProvider`)

```php
protected $listen = [
    ProduccionRegistrada::class => [
        CalcularKpisJornada::class,
        BroadcastKpisEnTiempoReal::class,
    ],
    JornadaFinalizada::class => [
        CalcularKpisFinalesJornada::class,
    ],
];
```

### 🔲 8.5 Configurar Laravel Echo (Frontend)

```bash
npm install --save-dev laravel-echo pusher-js
```

- [ ] Configurar `resources/js/echo.js`
- [ ] Importar en `resources/js/app.js`
- [ ] Compilar assets: `npm run build`

```javascript
// resources/js/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 🔲 8.6 Implementar Listeners en Vistas

```html
<script>
Echo.channel('maquina.{{ $maquina->id }}')
    .listen('.produccion.registrada', (e) => {
        // Actualizar UI en tiempo real
        document.getElementById('total-producidas').textContent = e.total_producidas;
    });
</script>
```

---

## 🎯 Fase 9: Jobs (Trabajos en Cola)

### 🔲 9.1 Crear Jobs

- [ ] `app/Jobs/CalcularKpisFinalesJornada.php`
- [ ] `app/Jobs/GenerarReporteKpi.php`

```bash
php artisan make:job CalcularKpisFinalesJornada
```

### 🔲 9.2 Configurar Cola

```bash
# .env
QUEUE_CONNECTION=database

# Crear tabla de jobs
php artisan queue:table
php artisan migrate
```

### 🔲 9.3 Ejecutar Worker

```bash
php artisan queue:work
```

---

## 🤖 Fase 10: Emulador de Máquinas

### 🔲 10.1 Comando Artisan

- [ ] `app/Console/Commands/EmuladorMaquinaCommand.php`

```bash
php artisan make:command EmuladorMaquinaCommand
```

**Funcionalidad:**
```bash
# Emular una máquina específica
php artisan emulator:maquina {maquina_id} --interval=5 --produccion=10

# Emular todas las máquinas
php artisan emulator:maquina --all --interval=10
```

### 🔲 10.2 Servicio de Emulación

- [ ] `app/Services/EmuladorService.php`
  - Generar producción aleatoria
  - Simular fallos ocasionales
  - Enviar requests a la API

### 🔲 10.3 Interfaz Web del Emulador

- [ ] Vista: `resources/views/emulator/index.blade.php`
- [ ] Controlador: `EmuladorController.php`
- [ ] Funciones:
  - Seleccionar máquina
  - Iniciar/Detener emulación
  - Enviar producción manual
  - Ver log en tiempo real

---

## 🎨 Fase 11: Diseño con Tailwind CSS

### 🔲 11.1 Configuración

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

- [ ] Configurar `tailwind.config.js`
- [ ] Configurar `resources/css/app.css`
- [ ] Compilar: `npm run dev`

### 🔲 11.2 Componentes UI

- [ ] Dashboard responsivo (grid/flexbox)
- [ ] Cards con sombras y animaciones
- [ ] Tablas con hover y striped
- [ ] Formularios con validación visual
- [ ] Modales con backdrop
- [ ] Toasts/Alertas
- [ ] Badges de estado (Verde/Rojo/Amarillo)
- [ ] Gráficos (Chart.js o ApexCharts)

### 🔲 11.3 Paleta de Colores

```javascript
// tailwind.config.js
theme: {
  extend: {
    colors: {
      'success': '#10b981',  // Verde (Running)
      'warning': '#f59e0b',  // Amarillo (Paused)
      'danger': '#ef4444',   // Rojo (Stopped)
      'idle': '#6b7280',     // Gris (Idle)
    }
  }
}
```

---

## 📊 Fase 12: Gráficos y Visualización

### 🔲 12.1 Instalar Librería de Gráficos

**Opción 1: Chart.js**
```bash
npm install chart.js
```

**Opción 2: ApexCharts**
```bash
npm install apexcharts
```

### 🔲 12.2 Implementar Gráficos

- [ ] Gráfico de OEE histórico (línea)
- [ ] Gráfico de disponibilidad (barra)
- [ ] Gráfico de producción por máquina (barra)
- [ ] Gráfico de calidad (dona)
- [ ] Timeline de eventos (custom)

---

## 🧪 Fase 13: Testing

### 🔲 13.1 Tests Unitarios

- [ ] `tests/Unit/Services/KpiServiceTest.php`
- [ ] `tests/Unit/Services/JornadaServiceTest.php`
- [ ] `tests/Unit/Repositories/JornadaProduccionRepositoryTest.php`

```bash
php artisan make:test Unit/Services/KpiServiceTest --unit
```

### 🔲 13.2 Tests de Feature

- [ ] `tests/Feature/Admin/MaquinaControllerTest.php`
- [ ] `tests/Feature/Supervisor/JornadaControllerTest.php`
- [ ] `tests/Feature/Api/V1/ProduccionControllerTest.php`

```bash
php artisan make:test Feature/Admin/MaquinaControllerTest
```

### 🔲 13.3 Ejecutar Tests

```bash
php artisan test
php artisan test --filter=JornadaServiceTest
```

---

## 🚀 Fase 14: Deployment

### 🔲 14.1 Preparación

- [ ] Optimizar autoload: `composer install --optimize-autoloader --no-dev`
- [ ] Optimizar config: `php artisan config:cache`
- [ ] Optimizar rutas: `php artisan route:cache`
- [ ] Optimizar vistas: `php artisan view:cache`
- [ ] Compilar assets: `npm run build`

### 🔲 14.2 Configuración Producción

- [ ] Configurar `.env` de producción
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Configurar base de datos
- [ ] Configurar Redis (opcional)
- [ ] Configurar supervisor para queue worker

### 🔲 14.3 Seguridad

- [ ] HTTPS habilitado
- [ ] CORS configurado
- [ ] Rate limiting en API
- [ ] Validar inputs
- [ ] Sanitizar outputs

---

## 📝 Checklist de Casos de Uso

### ✅ Caso 1: Admin define plan
- [ ] Controlador: `PlanMaquinaController@store`
- [ ] Request: `StorePlanMaquinaRequest`
- [ ] Servicio: `PlanMaquinaService::create()`
- [ ] Vista: `admin/planes/create.blade.php`

### ✅ Caso 2: Supervisor inicia jornada
- [ ] Controlador: `JornadaController@store`
- [ ] Request: `IniciarJornadaRequest`
- [ ] Servicio: `JornadaService::iniciarJornada()`
- [ ] Evento: `JornadaIniciada`
- [ ] Vista: `supervisor/jornadas/index.blade.php`

### ✅ Caso 3: Máquina registra producción
- [ ] API: `ProduccionController@store`
- [ ] Request: `RegistrarProduccionRequest`
- [ ] Servicio: `ProduccionService::registrar()`
- [ ] Repository: `RegistroProduccionRepository::create()`
- [ ] Evento: `ProduccionRegistrada`
- [ ] Broadcast: WebSocket en tiempo real

### ✅ Caso 4: Máquina se detiene por límite
- [ ] Lógica en: `ProduccionService::verificarLimiteFallos()`
- [ ] Evento: `MaquinaDetenidaCritica`
- [ ] Actualizar: `jornadas_produccion.status = 'stopped_critical'`
- [ ] Crear: `eventos_parada_jornada`

### ✅ Caso 5: Supervisor detiene máquina
- [ ] Controlador: `JornadaController@pausar`
- [ ] Request: `PausarJornadaRequest`
- [ ] Servicio: `JornadaService::pausarJornada()`

### ✅ Caso 6: Supervisor reanuda producción
- [ ] Controlador: `JornadaController@reanudar`
- [ ] Servicio: `JornadaService::reanudarJornada()`
- [ ] Actualizar: `eventos_parada_jornada.fin_parada`

### ✅ Caso 7: Admin visualiza KPI por máquina
- [ ] Controlador: `ReporteKpiController@maquina`
- [ ] Repository: `ResultadoKpiRepository::getByMaquina()`
- [ ] Vista: `admin/reportes/kpi-maquina.blade.php`

### ✅ Caso 8: Admin visualiza KPI por área
- [ ] Controlador: `ReporteKpiController@area`
- [ ] Repository: `ResultadoKpiRepository::getByArea()`
- [ ] Vista: `admin/reportes/kpi-area.blade.php`

### ✅ Caso 9: Admin visualiza historial de planes
- [ ] Controlador: `PlanMaquinaController@index`
- [ ] Repository: `PlanMaquinaRepository::getHistory()`
- [ ] Vista: `admin/planes/index.blade.php`

### ✅ Caso 10: Visualiza historial eventos/mantenimientos
- [ ] Controlador: `MonitorController@eventos`
- [ ] Combinar queries de `eventos_parada_jornada` y `registros_mantenimiento`
- [ ] Vista: `supervisor/monitor/eventos.blade.php`

---

## 📈 Métricas de Progreso

| Fase | Estado | Progreso |
|------|--------|----------|
| Fase 0: Documentación | ✅ | 100% |
| Fase 1: Base de Datos | 🔲 | 0% |
| Fase 2: Autenticación | 🔲 | 0% |
| Fase 3: Arquitectura | 🔲 | 0% |
| Fase 4: Form Requests | 🔲 | 0% |
| Fase 5: Controladores | 🔲 | 0% |
| Fase 6: Rutas | 🔲 | 0% |
| Fase 7: Vistas | 🔲 | 0% |
| Fase 8: WebSockets | 🔲 | 0% |
| Fase 9: Jobs | 🔲 | 0% |
| Fase 10: Emulador | 🔲 | 0% |
| Fase 11: Tailwind CSS | 🔲 | 0% |
| Fase 12: Gráficos | 🔲 | 0% |
| Fase 13: Testing | 🔲 | 0% |
| Fase 14: Deployment | 🔲 | 0% |

**Progreso Total:** 6.67% (1/15 fases completadas)

---

## 🎯 Próximos Pasos Inmediatos

1. ✅ Instalar dependencias (`composer install`, `npm install`)
2. ✅ Configurar `.env`
3. ✅ Crear migraciones faltantes
4. ✅ Ejecutar migraciones
5. ✅ Instalar Spatie Permission y Laravel Sanctum
6. ✅ Crear modelos con relaciones
7. ✅ Crear seeders y ejecutarlos
8. ✅ Implementar autenticación (Breeze/Jetstream)

---

**Última actualización:** 9 de noviembre de 2025  
**Mantenedor:** Tu Equipo de Desarrollo
