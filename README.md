# 🏭 KPI Dashboard Industrial

> **Sistema de Monitoreo de Producción Industrial en Tiempo Real**

Un dashboard moderno y en tiempo real para el monitoreo de indicadores clave de desempeño (KPI) de equipos industriales. Construido con **Laravel 11**, **Laravel Reverb (WebSockets)**, y **Tailwind CSS**.

![Laravel](https://img.shields.io/badge/Laravel-11.0-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.0-38B2AC?style=flat&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📚 Documentación Completa

### 🚀 Inicio Rápido
👉 **[INICIO.md](INICIO.md)** - Guía de inicio, comandos y configuración inicial

### 🧹 Limpieza de Plantilla
👉 **[LIMPIEZA-PLANTILLA.md](LIMPIEZA-PLANTILLA.md)** - Purgar dependencias innecesarias (Pusher, etc.)

### 🏗️ Arquitectura
👉 **[ARCHITECTURE.md](ARCHITECTURE.md)** - Estructura técnica, patrones y flujos de datos

### 📋 Plan de Acción
👉 **[plan-de-accion-check.md](plan-de-accion-check.md)** - Checklist completo de implementación

### 📖 Casos de Uso
👉 **[casos de usos.md](casos%20de%20usos.md)** - Flujos detallados de los 10 casos de uso principales

---

## ✨ Características Principales

## ✨ Características Principales

### 🎯 Monitoreo de KPIs en Tiempo Real
- **OEE (Overall Equipment Effectiveness)**: Métrica compuesta (Disponibilidad × Rendimiento × Calidad)
- **Disponibilidad**: Porcentaje de tiempo operativo del equipo
- **Rendimiento**: Velocidad de producción real vs teórica
- **Calidad**: Porcentaje de unidades sin defectos
- **Actualizaciones en vivo** vía WebSockets (Laravel Reverb)

### 👥 Sistema de Roles
- **Administrador**: Gestión completa del sistema, configuración de máquinas y planes, reportes globales
- **Supervisor**: Gestión de jornadas, mantenimientos, monitoreo de área
- **Máquina (API)**: Autenticación vía token para reporte automático de producción

### 🏭 Gestión de Producción
- **Planes de Producción**: Configuración de objetivos por máquina y turno
- **Jornadas de Trabajo**: Inicio/Fin automático con snapshot de objetivos
- **Registro de Producción**: Captura 1 a 1 o por lotes desde máquinas
- **Paradas Automáticas**: Detención por límite de fallos de calidad
- **Mantenimientos**: Registro de calibraciones, preventivos y correctivos

### 📊 Dashboard Interactivo
- **Vista por Máquina**: Métricas individuales e historial
- **Vista por Área**: KPIs agregados de múltiples máquinas
- **Gráficos en tiempo real**: Chart.js o ApexCharts
- **Componentes reutilizables**: Blade Components con Alpine.js
- **Diseño responsivo**: Tailwind CSS

### 🚀 Arquitectura Moderna
- **Repository Pattern**: Abstracción de acceso a datos
- **Service Layer**: Lógica de negocio separada
- **Event-Driven**: Eventos y Listeners para tiempo real
- **API Versionada**: `/api/v1/*` para máquinas
- **Form Requests**: Validación centralizada

### 🤖 Emulador de Máquinas
- **Interfaz Web**: Control manual de simulación
- **Comando Artisan**: `php artisan emulator:maquina {id}`
- **Producción automática**: Genera datos realistas para demos

---

## 🔧 Requisitos

- **PHP**: 8.2 o superior
- **Laravel**: 11.0
- **Composer**: 2.4+
- **Node.js**: 18.0+ y npm
- **MySQL**: 8.0+ (o compatible)
- **Redis** (opcional): Para cache y sessions

---

## 🚀 Instalación Rápida

### Opción 1: Setup Automático (Recomendado)

```bash
# Clonar repositorio
git clone <repository-url>
cd kpi-dashboard

# Instalar y configurar
composer run setup

# Iniciar servidor de desarrollo
composer run dev
```

### Opción 2: Instalación Manual

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar base de datos en .env
# DB_DATABASE=kpi_dashboard
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 5. Instalar dependencias frontend
npm install

# 6. Compilar assets
npm run build

# 7. Iniciar servicios
php artisan serve
php artisan reverb:start    # En otra terminal
php artisan queue:work      # En otra terminal
```

### Instalación de Paquetes Adicionales

```bash
# Spatie Permission (Roles)
composer require spatie/laravel-permission

# Laravel Reverb (WebSockets)
php artisan install:broadcasting

# Opcional: Herramientas de desarrollo
composer require --dev laravel/pint barryvdh/laravel-debugbar
```

---

## 📁 Estructura del Proyecto

Ver **[ARCHITECTURE.md](ARCHITECTURE.md)** para la estructura completa y detallada.

```
kpi-dashboard/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # Gestión de administrador
│   │   ├── Supervisor/         # Gestión de supervisor
│   │   └── Api/V1/Maquina/    # API para máquinas
│   ├── Services/               # Lógica de negocio
│   ├── Repositories/           # Acceso a datos
│   ├── Events/                 # Eventos del sistema
│   ├── Listeners/              # Listeners de eventos
│   └── Models/                 # Modelos Eloquent
├── resources/
│   ├── views/
│   │   ├── admin/             # Vistas de administrador
│   │   ├── supervisor/        # Vistas de supervisor
│   │   ├── components/        # Componentes Blade
│   │   └── emulator/          # Emulador de máquinas
│   └── js/
│       └── echo.js            # Laravel Echo (WebSockets)
├── database/
│   ├── migrations/            # Migraciones de BD
│   └── seeders/               # Datos de prueba
├── routes/
│   ├── web.php                # Rutas web
│   ├── api.php                # API versionada
│   └── channels.php           # Canales WebSocket
├── INICIO.md                  # 🚀 Guía de inicio
├── ARCHITECTURE.md            # 🏗️ Arquitectura
├── plan-de-accion-check.md    # ✅ Checklist
└── casos de usos.md           # 📖 Casos de uso
```

---

## ⚙️ Configuración

### Variables de Entorno Principales

```env
APP_NAME="KPI Dashboard Industrial"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_DATABASE=kpi_dashboard
DB_USERNAME=root
DB_PASSWORD=

# Laravel Reverb (WebSockets)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080

# Queue
QUEUE_CONNECTION=database

# Broadcasting
BROADCAST_DRIVER=reverb
```

Ver **[INICIO.md](INICIO.md)** para configuración detallada.

---

## 💻 Uso del Sistema

### Comandos de Desarrollo

```bash
# Iniciar todos los servicios concurrentemente
composer run dev

# O manualmente:
php artisan serve              # Servidor (http://localhost:8000)
php artisan reverb:start       # WebSockets
php artisan queue:work         # Cola de trabajos
npm run dev                    # Vite (hot reload)
```

### Usuarios de Prueba (después de seeders)

**Administrador:**
- Email: `admin@kpi-dashboard.com`
- Password: `password`

**Supervisor:**
- Email: `supervisor@kpi-dashboard.com`
- Password: `password`

### Emulador de Máquinas

**Interfaz Web:**
```
http://localhost:8000/emulator
```

**Comando Artisan:**
```bash
# Emular una máquina específica
php artisan emulator:maquina {maquina-uuid} --interval=5

# Emular todas las máquinas
php artisan emulator:maquina --all --interval=10
```

---

## 📡 API Endpoints

### Autenticación
Todas las rutas API usan **Laravel Sanctum** con tokens.

### Máquinas (`/api/v1/maquina/*`)

```http
POST   /api/v1/maquina/produccion     # Registrar producción
PUT    /api/v1/maquina/status         # Actualizar estado
POST   /api/v1/maquina/heartbeat      # Keep-alive
```

**Ejemplo de Request:**
```bash
curl -X POST http://localhost:8000/api/v1/maquina/produccion \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "cantidad_producida": 10,
    "cantidad_buena": 9,
    "cantidad_mala": 1
  }'
```

**Ejemplo de Response:**
```json
{
  "success": true,
  "data": {
    "registro_id": "uuid",
    "jornada": {
      "total_producidas": 100,
      "total_buenas": 92,
      "total_malas": 8,
      "progreso": 66.7
    }
  }
}
```

Ver **[ARCHITECTURE.md](ARCHITECTURE.md#api-versionada)** para documentación completa de la API.

---

## 🗄️ Base de Datos

### Tablas Principales

1. **areas** - Áreas de la fábrica
2. **maquinas** - Equipos/Máquinas
3. **planes_maquina** - Plantillas de configuración
4. **jornadas_produccion** - Turnos de trabajo (copia del plan)
5. **eventos_parada_jornada** - Registro de paradas
6. **registros_produccion** - Log de producción 1:1
7. **registros_mantenimiento** - Mantenimientos
8. **resultados_kpi_jornada** - KPIs pre-calculados

### Diagrama ER

Ver **[casos de usos.md](casos%20de%20usos.md#esquema-de-base-de-datos)** para el esquema completo en formato dbdiagram.io.

---

## 🏗️ Arquitectura y Patrones

### Flujo de Datos (Ejemplo: Registro de Producción)

```
[Máquina]
    ↓ POST /api/v1/maquina/produccion
[ProduccionController] → [RegistrarProduccionRequest]
    ↓
[ProduccionService::registrar()]
    ↓
[RegistroProduccionRepository::create()]
[JornadaProduccionRepository::incrementCounters()]
    ↓
[Event: ProduccionRegistrada]
    ↓
[Listener: BroadcastKpisEnTiempoReal]
    ↓ WebSocket (Laravel Reverb)
[Dashboard actualiza en vivo]
```

Ver **[ARCHITECTURE.md](ARCHITECTURE.md)** para documentación completa de la arquitectura.

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter=JornadaServiceTest
```

---

## 📋 Checklist de Implementación

Ver **[plan-de-accion-check.md](plan-de-accion-check.md)** para el plan completo de desarrollo con checkboxes.

**Fases:**
- ✅ Fase 0: Documentación (Completado)
- 🔲 Fase 1: Base de Datos y Modelos
- 🔲 Fase 2: Autenticación y Autorización
- 🔲 Fase 3: Arquitectura (Repositories + Services)
- 🔲 Fase 4: Form Requests
- 🔲 Fase 5: Controladores
- 🔲 Fase 6: Rutas
- 🔲 Fase 7: Vistas (Blade + Tailwind)
- 🔲 Fase 8: Eventos y WebSockets
- 🔲 Fase 9: Jobs
- 🔲 Fase 10: Emulador
- 🔲 Fase 11: Diseño con Tailwind
- 🔲 Fase 12: Gráficos
- 🔲 Fase 13: Testing
- 🔲 Fase 14: Deployment

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crear rama: `git checkout -b feature/AmazingFeature`
3. Commit: `git commit -m 'feat: add amazing feature'`
4. Push: `git push origin feature/AmazingFeature`
5. Abrir Pull Request

### Convenciones
- Seguir [Conventional Commits](https://www.conventionalcommits.org/)
- Usar Laravel Pint para formateo: `composer run lint`
- Escribir tests para nuevas features

---

## 📦 Dependencias Principales

### Backend
- `laravel/framework` - Framework
- `laravel/sanctum` - Autenticación API
- `spatie/laravel-permission` - Roles y permisos
- `laravel/reverb` - WebSockets

### Frontend
- `tailwindcss` - CSS utility-first
- `alpinejs` - Framework JS ligero
- `laravel-echo` - Cliente WebSocket
- `chart.js` / `apexcharts` - Gráficos

---

## 🐛 Troubleshooting

### Error: "Application key missing"
```bash
php artisan key:generate
```

### Error: Base de Datos
```bash
# Recrear base de datos
php artisan migrate:fresh --seed
```

### WebSockets no funcionan
```bash
# Verificar que Reverb esté corriendo
php artisan reverb:start

# Verificar variables en .env
BROADCAST_DRIVER=reverb
```

Ver **[INICIO.md](INICIO.md#🆘-soporte)** para más soluciones.

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

## 📞 Soporte y Contacto

- 📖 **Documentación**: Ver archivos `.md` en la raíz del proyecto
- 🐛 **Issues**: Abrir issue en el repositorio
- 💬 **Discusiones**: [GitHub Discussions]

---

**Desarrollado con ❤️ usando Laravel, Tailwind CSS y Laravel Reverb**

📅 **Última actualización:** 9 de noviembre de 2025
