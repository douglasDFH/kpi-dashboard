# 🚀 Inicio Rápido - KPI Dashboard Industrial

## 📋 Índice de Documentación

### 📚 Documentación Principal
- **[Limpieza de Plantilla](LIMPIEZA-PLANTILLA.md)** - 🧹 Purgar dependencias innecesarias
- **[Plan de Acción](plan-de-accion-check.md)** - Lista de verificación completa del proyecto
- **[Arquitectura](ARCHITECTURE.md)** - Estructura técnica y patrones de diseño
- **[Casos de Uso](casos%20de%20usos.md)** - Flujos de datos y procesos del sistema
- **[Resumen Ejecutivo](docs/ANALISIS-RESUMEN-EJECUTIVO.md)** - Análisis del proyecto

---

## 🎯 Resumen del Proyecto

**Sistema de Monitoreo KPI Industrial en Tiempo Real**

Dashboard para monitorear la producción de máquinas industriales con cálculo de OEE (Overall Equipment Effectiveness) en tiempo real.

### Stack Tecnológico
- **Backend:** Laravel 11 + Laravel Reverb (WebSockets)
- **Frontend:** Blade + Alpine.js + Tailwind CSS + Laravel Echo
- **Base de Datos:** MySQL con UUIDs
- **API:** RESTful versionada (`/api/v1/*`)
- **Autenticación:** Laravel Sanctum (para máquinas)

---

## 👥 Roles del Sistema

### 1. **Administrador**
- Configurar máquinas y planes de producción
- Visualizar KPIs históricos y en tiempo real
- Gestionar usuarios y permisos
- Acceder a reportes globales

### 2. **Supervisor/Encargado**
- Iniciar/Finalizar jornadas de producción
- Pausar/Reanudar máquinas
- Registrar mantenimientos
- Ver KPIs de su área

### 3. **Máquina (API)**
- Autenticación vía token (Sanctum)
- Reportar producción en tiempo real
- Recibir comandos de pausa/detención

---

## 🗂️ Estructura Principal

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Controladores de Administrador
│   │   ├── Supervisor/      # Controladores de Supervisor
│   │   └── Api/
│   │       └── V1/
│   │           └── Maquina/ # API para máquinas
│   ├── Requests/            # Form Requests personalizados
│   └── Middleware/          # Middlewares personalizados
├── Services/                # Lógica de negocio
├── Repositories/            # Capa de acceso a datos
├── Models/                  # Modelos Eloquent
├── Events/                  # Eventos del sistema
├── Listeners/               # Listeners de eventos
└── Jobs/                    # Trabajos en cola

resources/
├── views/
│   ├── admin/              # Vistas de administrador
│   ├── supervisor/         # Vistas de supervisor
│   ├── components/         # Componentes reutilizables
│   └── emulator/           # Emulador de máquinas
└── js/
    └── echo.js             # Configuración Laravel Echo

routes/
├── web.php                 # Rutas web
├── api.php                 # Rutas API (versionadas)
├── api/v1.php              # Rutas api V1                  
└── channels.php            # Canales WebSocket
```

---

## 🔑 Rutas Principales

### Web (Autenticadas)
```
/admin/*          → Panel de Administración
/supervisor/*     → Panel de Supervisor
/emulator         → Emulador de Máquinas (Demo)
```

### API v1 (Token Sanctum)
```
/api/v1/maquina/produccion     → Registrar producción
/api/v1/maquina/status         → Actualizar estado
/api/v1/maquina/heartbeat      → Keep-alive
```

---

## 🚀 Comandos Disponibles

### Desarrollo
```bash
# Iniciar servidor de desarrollo
php artisan serve

# Iniciar Laravel Reverb (WebSockets)
php artisan reverb:start

# Iniciar cola de trabajos
php artisan queue:work

# Compilar assets (Vite)
npm run dev
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Refrescar BD con datos de prueba
php artisan migrate:fresh --seed
```

### Emulador
```bash
# Emular una máquina (producción automática)
php artisan emulator:maquina {maquina_id} --interval=5

# Emular múltiples máquinas
php artisan emulator:maquina --all --interval=10
```

---

## 📦 Instalación Inicial

### Paso 1: Purgar Dependencias Innecesarias de la Plantilla

```bash
# 1. Clonar repositorio (si aplica)
git clone <repo-url>
cd kpi-dashboard

# 2. PRIMERO: Remover librerías que NO usaremos
# Remover Pusher (usaremos Laravel Reverb en su lugar)
composer remove pusher/pusher-php-server
npm uninstall pusher-js laravel-echo

# Opcional: Remover DomPDF si no generarás PDFs
composer remove barryvdh/laravel-dompdf

# Opcional: Remover Laravel Sail si no usas Docker
composer remove --dev laravel/sail
```

### Paso 2: Instalar Dependencias Limpias

```bash
# 3. Instalar dependencias PHP restantes
composer install

# 4. Instalar dependencias Node restantes
npm install

# 5. Configurar entorno
cp .env.example .env
php artisan key:generate

# 6. Configurar base de datos en .env
# DB_DATABASE=kpi_dashboard
# DB_USERNAME=root
# DB_PASSWORD=
```

### Paso 3: Instalar Paquetes Necesarios

```bash
# Spatie Permission (Roles y Permisos)
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Laravel Reverb (WebSockets - mejor que Pusher)
php artisan install:broadcasting
# Esto instalará automáticamente:
# - laravel/reverb (Composer)
# - laravel-echo (npm)
# - pusher-js (npm - necesario para el protocolo)

# Alpine.js (interactividad frontend)
npm install alpinejs

# Chart.js o ApexCharts (gráficos) - Elegir UNO
npm install chart.js
# O
npm install apexcharts
```

### Paso 4: Configurar Base de Datos

```bash
# 7. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 8. Crear enlace simbólico de storage
php artisan storage:link

# 9. Compilar assets
npm run build
```

### Paso 5: Configurar Laravel Reverb

```bash
# Agregar al .env (después de install:broadcasting)
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Las credenciales se generan automáticamente con install:broadcasting
```

---

## ⚙️ Verificar Instalación

```bash
# Ver paquetes instalados
composer show
npm list --depth=0

# Verificar que NO estén:
# ❌ pusher/pusher-php-server
# ❌ barryvdh/laravel-dompdf (si lo removiste)

# Verificar que SÍ estén:
# ✅ laravel/reverb
# ✅ spatie/laravel-permission
# ✅ laravel/sanctum
```

---

## 🔐 Usuarios de Prueba (Después de Seeders)

### Administrador
- **Email:** admin@kpi-dashboard.com
- **Password:** password

### Supervisor
- **Email:** supervisor@kpi-dashboard.com
- **Password:** password

### Tokens de Máquinas
Los tokens se generan automáticamente para cada máquina en el seeder.

---

## 🧪 Emulador de Máquinas

### Interfaz Web
Accede a `/emulator` para controlar manualmente las máquinas simuladas.

### Línea de Comandos
```bash
# Emular Prensa 1 cada 5 segundos
php artisan emulator:maquina prensa-1-uuid --interval=5

# Emular todas las máquinas
php artisan emulator:maquina --all
```

---

## 📊 Casos de Uso Principales

1. **Admin configura plan** → Define objetivos para una máquina
2. **Supervisor inicia jornada** → Comienza turno de trabajo
3. **Máquina reporta producción** → Envía datos en tiempo real
4. **Sistema calcula KPIs** → OEE, Disponibilidad, Rendimiento, Calidad
5. **Dashboard actualiza** → WebSocket actualiza vistas en vivo
6. **Supervisor finaliza jornada** → Se calculan KPIs finales

---

## 🔥 Características Principales

### ✅ Tiempo Real
- Dashboard actualizado vía WebSockets (Laravel Reverb)
- Notificaciones instantáneas de paradas críticas
- Monitoreo en vivo del estado de máquinas

### ✅ Arquitectura Escalable
- Patrón Repository
- Service Layer
- Event-Driven Architecture
- API Versionada

### ✅ Seguridad
- Autenticación de usuarios (Laravel Breeze/Jetstream)
- Tokens API para máquinas (Sanctum)
- Roles y permisos (Spatie Permission)
- Form Requests con validación

### ✅ Rendimiento
- Datos agregados en `jornadas_produccion`
- Reportes pre-calculados en `resultados_kpi_jornada`
- Jobs en cola para cálculos pesados
- Índices optimizados en BD

---

## 📝 Próximos Pasos

1. ✅ Revisar [Plan de Acción](plan-de-accion-check.md)
2. ✅ Estudiar [Arquitectura](ARCHITECTURE.md)
3. ✅ Implementar autenticación y roles
4. ✅ Crear Request personalizados
5. ✅ Implementar Services y Repositories
6. ✅ Desarrollar controladores por roles
7. ✅ Crear vistas con Tailwind CSS
8. ✅ Implementar WebSockets
9. ✅ Desarrollar emulador
10. ✅ Pruebas y deployment

---

## 🆘 Soporte

Para más información, revisa la documentación en la carpeta `/docs` o consulta los archivos:
- `ARCHITECTURE.md` - Detalles técnicos
- `plan-de-accion-check.md` - Checklist de implementación
- `casos de usos.md` - Flujos del sistema

---

**Última actualización:** 9 de noviembre de 2025
