# 🏗️ Arquitectura - KPI Dashboard Industrial

## 📋 Tabla de Contenidos

1. [Visión General](#visión-general)
2. [Patrones de Diseño](#patrones-de-diseño)
3. [Estructura de Carpetas](#estructura-de-carpetas)
4. [Capas de la Aplicación](#capas-de-la-aplicación)
5. [Flujo de Datos](#flujo-de-datos)
6. [API Versionada](#api-versionada)
7. [Eventos y WebSockets](#eventos-y-websockets)
8. [Base de Datos](#base-de-datos)

---

## 🎯 Visión General

### Principios Arquitectónicos

- **MVC Extendido:** Model-View-Controller con capas adicionales
- **Event-Driven:** Arquitectura basada en eventos
- **Repository Pattern:** Abstracción de acceso a datos
- **Service Layer:** Lógica de negocio separada
- **API First:** API RESTful versionada para máquinas

### Stack Tecnológico

```
┌─────────────────────────────────────────────┐
│           Frontend (Blade + Alpine.js)      │
│              Tailwind CSS + Laravel Echo    │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│         Laravel 11 (Backend)                │
│  Controllers → Services → Repositories      │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│    Laravel Reverb (WebSockets Real-Time)    │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│         MySQL Database (UUIDs)              │
└─────────────────────────────────────────────┘
```

---

## 🎨 Patrones de Diseño

### 1. Repository Pattern

**Propósito:** Abstraer la lógica de acceso a datos.

```php
// app/Repositories/JornadaProduccionRepository.php
interface JornadaProduccionRepositoryInterface
{
    public function getActive(string $maquinaId): ?JornadaProduccion;
    public function create(array $data): JornadaProduccion;
    public function updateStatus(string $id, string $status): bool;
}
```

### 2. Service Layer

**Propósito:** Encapsular lógica de negocio compleja.

```php
// app/Services/JornadaService.php
class JornadaService
{
    public function iniciarJornada(string $maquinaId, int $supervisorId): JornadaProduccion
    {
        // Lógica compleja de negocio
        // Validaciones, cálculos, eventos, etc.
    }
}
```

### 3. Event-Driven Architecture

**Propósito:** Desacoplar componentes mediante eventos.

```php
// Disparar evento
event(new ProduccionRegistrada($registro));

// Escuchar evento
class ActualizarKpisEnTiempoReal implements ShouldQueue
{
    public function handle(ProduccionRegistrada $event) { }
}
```

### 4. Form Request Pattern

**Propósito:** Validación centralizada y autorización.

```php
// app/Http/Requests/RegistrarProduccionRequest.php
class RegistrarProduccionRequest extends FormRequest
{
    public function authorize(): bool { }
    public function rules(): array { }
}
```

---

## 📁 Estructura de Carpetas

### Estructura Completa del Proyecto

```
kpi-dashboard/
│
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── EmuladorMaquinaCommand.php
│   │
│   ├── Events/
│   │   ├── JornadaIniciada.php
│   │   ├── JornadaFinalizada.php
│   │   ├── ProduccionRegistrada.php
│   │   ├── MaquinaDetenidaCritica.php
│   │   └── KpisActualizados.php
│   │
│   ├── Listeners/
│   │   ├── CalcularKpisJornada.php
│   │   ├── NotificarParadaCritica.php
│   │   └── BroadcastKpisEnTiempoReal.php
│   │
│   ├── Jobs/
│   │   ├── CalcularKpisFinalesJornada.php
│   │   └── GenerarReporteKpi.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── MaquinaController.php
│   │   │   │   ├── PlanMaquinaController.php
│   │   │   │   ├── AreaController.php
│   │   │   │   ├── ReporteKpiController.php
│   │   │   │   └── UsuarioController.php
│   │   │   │
│   │   │   ├── Supervisor/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── JornadaController.php
│   │   │   │   ├── MantenimientoController.php
│   │   │   │   └── MonitorController.php
│   │   │   │
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   │       └── Maquina/
│   │   │   │           ├── ProduccionController.php
│   │   │   │           ├── StatusController.php
│   │   │   │           └── HeartbeatController.php
│   │   │   │
│   │   │   └── EmuladorController.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── Admin/
│   │   │   │   ├── StorePlanMaquinaRequest.php
│   │   │   │   ├── UpdatePlanMaquinaRequest.php
│   │   │   │   ├── StoreMaquinaRequest.php
│   │   │   │   └── StoreAreaRequest.php
│   │   │   │
│   │   │   ├── Supervisor/
│   │   │   │   ├── IniciarJornadaRequest.php
│   │   │   │   ├── FinalizarJornadaRequest.php
│   │   │   │   ├── PausarJornadaRequest.php
│   │   │   │   └── RegistrarMantenimientoRequest.php
│   │   │   │
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── RegistrarProduccionRequest.php
│   │   │           └── ActualizarStatusRequest.php
│   │   │
│   │   └── Middleware/
│   │       ├── EnsureUserHasRole.php
│   │       └── ValidateMaquinaToken.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Area.php
│   │   ├── Maquina.php
│   │   ├── PlanMaquina.php
│   │   ├── JornadaProduccion.php
│   │   ├── EventoParadaJornada.php
│   │   ├── RegistroProduccion.php
│   │   ├── RegistroMantenimiento.php
│   │   └── ResultadoKpiJornada.php
│   │
│   ├── Services/
│   │   ├── JornadaService.php
│   │   ├── ProduccionService.php
│   │   ├── KpiService.php
│   │   ├── MantenimientoService.php
│   │   └── EmuladorService.php
│   │
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── JornadaProduccionRepositoryInterface.php
│   │   │   ├── RegistroProduccionRepositoryInterface.php
│   │   │   ├── MaquinaRepositoryInterface.php
│   │   │   ├── PlanMaquinaRepositoryInterface.php
│   │   │   └── ResultadoKpiRepositoryInterface.php
│   │   │
│   │   └── Eloquent/
│   │       ├── JornadaProduccionRepository.php
│   │       ├── RegistroProduccionRepository.php
│   │       ├── MaquinaRepository.php
│   │       ├── PlanMaquinaRepository.php
│   │       └── ResultadoKpiRepository.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── EventServiceProvider.php
│       └── RepositoryServiceProvider.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── supervisor.blade.php
│   │   │
│   │   ├── components/
│   │   │   ├── kpi-card.blade.php
│   │   │   ├── maquina-status.blade.php
│   │   │   ├── chart-oee.blade.php
│   │   │   ├── timeline-eventos.blade.php
│   │   │   └── tabla-produccion.blade.php
│   │   │
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── maquinas/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   ├── planes/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   └── reportes/
│   │   │       ├── kpi-maquina.blade.php
│   │   │       └── kpi-area.blade.php
│   │   │
│   │   ├── supervisor/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── jornadas/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── monitor.blade.php
│   │   │   └── mantenimiento/
│   │   │       └── create.blade.php
│   │   │
│   │   └── emulator/
│   │       └── index.blade.php
│   │
│   ├── js/
│   │   ├── app.js
│   │   ├── echo.js
│   │   └── components/
│   │       └── emulator.js
│   │
│   └── css/
│       └── app.css
│
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── channels.php
│   └── console.php
│
├── database/
│   ├── migrations/
│   │   ├── 2025_11_09_create_areas_table.php
│   │   ├── 2025_11_09_create_maquinas_table.php
│   │   ├── 2025_11_09_create_planes_maquina_table.php
│   │   ├── 2025_11_09_create_jornadas_produccion_table.php
│   │   ├── 2025_11_09_create_eventos_parada_jornada_table.php
│   │   ├── 2025_11_09_create_registros_produccion_table.php
│   │   ├── 2025_11_09_create_registros_mantenimiento_table.php
│   │   └── 2025_11_09_create_resultados_kpi_jornada_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       ├── UserSeeder.php
│       ├── AreaSeeder.php
│       ├── MaquinaSeeder.php
│       └── PlanMaquinaSeeder.php
│
├── tests/
│   ├── Feature/
│   │   ├── Admin/
│   │   ├── Supervisor/
│   │   └── Api/V1/
│   └── Unit/
│       ├── Services/
│       └── Repositories/
│
├── INICIO.md
├── ARCHITECTURE.md
├── plan-de-accion-check.md
└── casos de usos.md
```

---

## 🔄 Capas de la Aplicación

### 1️⃣ Capa de Presentación (Views)

**Responsabilidad:** Interfaz de usuario, componentes visuales.

```
resources/views/
├── admin/          → Vistas de administrador
├── supervisor/     → Vistas de supervisor
├── components/     → Componentes Blade reutilizables
└── emulator/       → Interfaz del emulador
```

**Tecnologías:**
- Blade Templates
- Alpine.js (interactividad)
- Tailwind CSS (estilos)
- Laravel Echo (WebSockets)

---

### 2️⃣ Capa de Controladores (Controllers)

**Responsabilidad:** Recibir requests, delegar a servicios, retornar responses.

#### Admin Controllers
```php
app/Http/Controllers/Admin/
├── DashboardController.php       → Vista general del sistema
├── MaquinaController.php         → CRUD de máquinas
├── PlanMaquinaController.php     → Gestión de planes
├── AreaController.php            → CRUD de áreas
├── ReporteKpiController.php      → Visualización de reportes
└── UsuarioController.php         → Gestión de usuarios
```

#### Supervisor Controllers
```php
app/Http/Controllers/Supervisor/
├── DashboardController.php       → Dashboard del supervisor
├── JornadaController.php         → Iniciar/Finalizar jornadas
├── MantenimientoController.php   → Registrar mantenimientos
└── MonitorController.php         → Monitor en tiempo real
```

#### API Controllers (Máquinas)
```php
app/Http/Controllers/Api/V1/Maquina/
├── ProduccionController.php      → POST /api/v1/maquina/produccion
├── StatusController.php          → PUT /api/v1/maquina/status
└── HeartbeatController.php       → POST /api/v1/maquina/heartbeat
```

**Ejemplo de Controlador:**
```php
namespace App\Http\Controllers\Admin;

class MaquinaController extends Controller
{
    public function __construct(
        private MaquinaService $maquinaService
    ) {}

    public function index()
    {
        $maquinas = $this->maquinaService->getAll();
        return view('admin.maquinas.index', compact('maquinas'));
    }

    public function store(StoreMaquinaRequest $request)
    {
        $maquina = $this->maquinaService->create($request->validated());
        return redirect()->route('admin.maquinas.index')
            ->with('success', 'Máquina creada exitosamente');
    }
}
```

---

### 3️⃣ Capa de Servicios (Services)

**Responsabilidad:** Lógica de negocio, orquestación, eventos.

```php
app/Services/
├── JornadaService.php           → Gestión de jornadas
├── ProduccionService.php        → Registro de producción
├── KpiService.php               → Cálculos de KPIs
├── MantenimientoService.php     → Gestión de mantenimientos
└── EmuladorService.php          → Lógica del emulador
```

**Ejemplo de Servicio:**
```php
namespace App\Services;

class JornadaService
{
    public function __construct(
        private JornadaProduccionRepositoryInterface $jornadaRepo,
        private PlanMaquinaRepositoryInterface $planRepo,
        private MaquinaRepositoryInterface $maquinaRepo
    ) {}

    public function iniciarJornada(string $maquinaId, int $supervisorId): JornadaProduccion
    {
        // 1. Validar que no haya jornada activa
        if ($this->jornadaRepo->getActive($maquinaId)) {
            throw new \Exception('Ya existe una jornada activa para esta máquina');
        }

        // 2. Obtener plan activo
        $plan = $this->planRepo->getActivePlan($maquinaId);
        if (!$plan) {
            throw new \Exception('No hay plan activo para esta máquina');
        }

        // 3. Crear jornada (snapshot del plan)
        $jornada = $this->jornadaRepo->create([
            'plan_maquina_id' => $plan->id,
            'maquina_id' => $maquinaId,
            'supervisor_id' => $supervisorId,
            'status' => 'running',
            'inicio_real' => now(),
            'objetivo_unidades_copiado' => $plan->objetivo_unidades,
            'unidad_medida_copiado' => $plan->unidad_medida,
            'limite_fallos_critico_copiado' => $plan->limite_fallos_critico,
        ]);

        // 4. Actualizar estado de máquina
        $this->maquinaRepo->updateStatus($maquinaId, 'running');

        // 5. Disparar evento
        event(new JornadaIniciada($jornada));

        return $jornada;
    }

    public function finalizarJornada(string $jornadaId): JornadaProduccion
    {
        // 1. Obtener jornada activa
        $jornada = $this->jornadaRepo->find($jornadaId);
        
        // 2. Actualizar estado
        $this->jornadaRepo->updateStatus($jornadaId, 'completed');
        $this->jornadaRepo->update($jornadaId, ['fin_real' => now()]);

        // 3. Actualizar máquina
        $this->maquinaRepo->updateStatus($jornada->maquina_id, 'idle');

        // 4. Disparar evento (que ejecutará Job de cálculo de KPIs)
        event(new JornadaFinalizada($jornada->fresh()));

        return $jornada->fresh();
    }
}
```

---

### 4️⃣ Capa de Repositorios (Repositories)

**Responsabilidad:** Acceso a datos, queries, abstracción de Eloquent.

```php
app/Repositories/
├── Contracts/                    → Interfaces
│   ├── JornadaProduccionRepositoryInterface.php
│   ├── RegistroProduccionRepositoryInterface.php
│   └── ...
└── Eloquent/                     → Implementaciones
    ├── JornadaProduccionRepository.php
    ├── RegistroProduccionRepository.php
    └── ...
```

**Ejemplo de Repository:**
```php
namespace App\Repositories\Eloquent;

class JornadaProduccionRepository implements JornadaProduccionRepositoryInterface
{
    public function __construct(
        private JornadaProduccion $model
    ) {}

    public function getActive(string $maquinaId): ?JornadaProduccion
    {
        return $this->model
            ->where('maquina_id', $maquinaId)
            ->where('status', 'running')
            ->first();
    }

    public function create(array $data): JornadaProduccion
    {
        return $this->model->create($data);
    }

    public function updateStatus(string $id, string $status): bool
    {
        return $this->model
            ->where('id', $id)
            ->update(['status' => $status]);
    }

    public function incrementCounters(string $id, array $counters): bool
    {
        $jornada = $this->model->find($id);
        foreach ($counters as $field => $value) {
            $jornada->increment($field, $value);
        }
        return true;
    }
}
```

---

### 5️⃣ Capa de Modelos (Models)

**Responsabilidad:** Representación de entidades, relaciones, casts.

```php
namespace App\Models;

class JornadaProduccion extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'jornadas_produccion';

    protected $fillable = [
        'plan_maquina_id',
        'maquina_id',
        'supervisor_id',
        'status',
        'inicio_real',
        'fin_real',
        'objetivo_unidades_copiado',
        'unidad_medida_copiado',
        'limite_fallos_critico_copiado',
        'total_unidades_producidas',
        'total_unidades_buenas',
        'total_unidades_malas',
    ];

    protected $casts = [
        'inicio_real' => 'datetime',
        'fin_real' => 'datetime',
        'objetivo_unidades_copiado' => 'integer',
        'limite_fallos_critico_copiado' => 'integer',
    ];

    // Relaciones
    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function planMaquina()
    {
        return $this->belongsTo(PlanMaquina::class);
    }

    public function registrosProduccion()
    {
        return $this->hasMany(RegistroProduccion::class, 'jornada_id');
    }

    public function eventosParada()
    {
        return $this->hasMany(EventoParadaJornada::class, 'jornada_id');
    }

    // Accessors
    public function getProgresoAttribute(): float
    {
        if ($this->objetivo_unidades_copiado == 0) return 0;
        return ($this->total_unidades_producidas / $this->objetivo_unidades_copiado) * 100;
    }
}
```

---

## 🔄 Flujo de Datos

### Caso 1: Máquina Registra Producción

```
[Máquina] 
    ↓ POST /api/v1/maquina/produccion
[ProduccionController]
    ↓ RegistrarProduccionRequest (validación)
[ProduccionService::registrar()]
    ↓
[RegistroProduccionRepository::create()]
[JornadaProduccionRepository::incrementCounters()]
    ↓
[Event: ProduccionRegistrada]
    ↓
[Listener: BroadcastKpisEnTiempoReal]
    ↓ WebSocket (Laravel Reverb)
[Dashboard Frontend actualiza en vivo]
```

### Caso 2: Supervisor Inicia Jornada

```
[Supervisor Web]
    ↓ POST /supervisor/jornadas
[JornadaController]
    ↓ IniciarJornadaRequest
[JornadaService::iniciarJornada()]
    ↓
[JornadaProduccionRepository::create()]
[PlanMaquinaRepository::getActivePlan()]
    ↓
[Event: JornadaIniciada]
    ↓ Broadcast WebSocket
[Redirección + Mensaje Flash]
```

---

## 🌐 API Versionada

### Estructura de Rutas API

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    
    // Rutas protegidas con Sanctum (token de máquina)
    Route::middleware(['auth:sanctum', 'ability:maquina'])->group(function () {
        
        Route::prefix('maquina')->name('api.v1.maquina.')->group(function () {
            
            // Registrar producción
            Route::post('/produccion', [ProduccionController::class, 'store'])
                ->name('produccion.store');
            
            // Actualizar status
            Route::put('/status', [StatusController::class, 'update'])
                ->name('status.update');
            
            // Heartbeat (keep-alive)
            Route::post('/heartbeat', [HeartbeatController::class, 'ping'])
                ->name('heartbeat');
        });
    });
});
```

### Autenticación API (Sanctum)

**Generar Token para Máquina:**
```php
// En Seeder o comando artisan
$maquina = Maquina::find('uuid-prensa-1');
$token = $maquina->createToken('maquina-token', ['maquina'])->plainTextToken;
```

**Request desde Máquina:**
```bash
curl -X POST https://kpi-dashboard.test/api/v1/maquina/produccion \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "cantidad_producida": 10,
    "cantidad_buena": 9,
    "cantidad_mala": 1
  }'
```

---

## 🔥 Eventos y WebSockets

### Eventos del Sistema

```php
app/Events/
├── JornadaIniciada.php          → Cuando supervisor inicia jornada
├── JornadaFinalizada.php        → Cuando supervisor finaliza jornada
├── ProduccionRegistrada.php     → Cada vez que máquina reporta
├── MaquinaDetenidaCritica.php   → Parada automática por QA
└── KpisActualizados.php         → KPIs recalculados
```

### Listeners

```php
app/Listeners/
├── CalcularKpisJornada.php      → Calcula KPIs en tiempo real
├── NotificarParadaCritica.php   → Notifica a supervisores
└── BroadcastKpisEnTiempoReal.php → Broadcast vía WebSocket
```

### Ejemplo de Evento

```php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProduccionRegistrada implements ShouldBroadcast
{
    use InteractsWithSockets;

    public function __construct(
        public RegistroProduccion $registro,
        public JornadaProduccion $jornada
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('maquina.' . $this->jornada->maquina_id);
    }

    public function broadcastAs(): string
    {
        return 'produccion.registrada';
    }

    public function broadcastWith(): array
    {
        return [
            'jornada_id' => $this->jornada->id,
            'total_producidas' => $this->jornada->total_unidades_producidas,
            'total_buenas' => $this->jornada->total_unidades_buenas,
            'total_malas' => $this->jornada->total_unidades_malas,
            'progreso' => $this->jornada->progreso,
        ];
    }
}
```

### Configuración Laravel Echo (Frontend)

```javascript
// resources/js/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

// Escuchar canal de máquina
window.Echo.channel(`maquina.${maquinaId}`)
    .listen('.produccion.registrada', (e) => {
        console.log('Nueva producción:', e);
        // Actualizar UI
        actualizarKpis(e);
    });
```

---

## 🗄️ Base de Datos

### Diagrama ER Simplificado

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │
       │ supervisor_id
       │
┌──────▼──────────────────┐      ┌──────────────────┐
│  jornadas_produccion    │◄─────┤  planes_maquina  │
└──────┬──────────────────┘      └──────────────────┘
       │                                  │
       │ jornada_id                       │ maquina_id
       │                                  │
       ├──────┬──────────────────────────┤
       │      │                           │
┌──────▼────┐ │ ┌────────────────┐ ┌────▼─────────┐
│ registros │ │ │ eventos_parada │ │   maquinas   │
│ produccion│ │ │    _jornada    │ └──────────────┘
└───────────┘ │ └────────────────┘
              │
       ┌──────▼─────────────┐
       │ resultados_kpi     │
       │    _jornada        │
       └────────────────────┘
```

### Índices Importantes

```sql
-- Para búsquedas rápidas de jornadas activas
CREATE INDEX idx_jornadas_maquina_status 
ON jornadas_produccion(maquina_id, status);

-- Para reportes históricos
CREATE INDEX idx_resultados_maquina_fecha 
ON resultados_kpi_jornada(maquina_id, fecha_jornada);

-- Para agregación de producción
CREATE INDEX idx_registros_jornada_created 
ON registros_produccion(jornada_id, created_at);
```

---

## 🎭 Roles y Permisos

### Definición de Roles

```php
// database/seeders/RoleSeeder.php
$admin = Role::create(['name' => 'admin']);
$supervisor = Role::create(['name' => 'supervisor']);

// Permisos para Admin
$admin->givePermissionTo([
    'view-dashboard',
    'manage-maquinas',
    'manage-planes',
    'manage-areas',
    'view-all-reportes',
    'manage-users',
]);

// Permisos para Supervisor
$supervisor->givePermissionTo([
    'view-dashboard',
    'manage-jornadas',
    'register-mantenimiento',
    'view-own-area-reportes',
]);
```

### Middleware de Roles

```php
// app/Http/Middleware/EnsureUserHasRole.php
public function handle($request, Closure $next, string $role)
{
    if (!$request->user()->hasRole($role)) {
        abort(403, 'No tienes permisos para acceder a esta sección');
    }
    return $next($request);
}
```

### Aplicar en Rutas

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::resource('maquinas', MaquinaController::class);
});

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index']);
    Route::resource('jornadas', JornadaController::class);
});
```

---

## 🧪 Emulador de Máquinas

### Comando Artisan

```php
// app/Console/Commands/EmuladorMaquinaCommand.php
php artisan emulator:maquina {maquina_id} --interval=5 --produccion=10
```

### Interfaz Web

```
GET /emulator → Vista con controles
POST /emulator/start → Iniciar simulación
POST /emulator/stop → Detener simulación
POST /emulator/produccion → Enviar producción manual
```

---

## 📊 Resumen

| Aspecto | Tecnología/Patrón |
|---------|-------------------|
| **Framework** | Laravel 11 |
| **Frontend** | Blade + Alpine.js + Tailwind CSS |
| **WebSockets** | Laravel Reverb + Laravel Echo |
| **API** | RESTful versionada (v1) |
| **Autenticación** | Laravel Sanctum |
| **Roles** | Spatie Permission |
| **Patrón** | Repository + Service Layer |
| **Eventos** | Event-Driven Architecture |
| **BD** | MySQL con UUIDs |
| **Validación** | Form Requests |

---

**Última actualización:** 9 de noviembre de 2025
