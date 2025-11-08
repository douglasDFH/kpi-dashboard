# KPI Dashboard Industrial

Un dashboard moderno y en tiempo real para el monitoreo de indicadores clave de desempeño (KPI) de equipos industriales. Construido con **Laravel 12** y **Vite**, proporciona métricas de eficiencia operativa, disponibilidad y calidad.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4.0-38B2AC?style=flat&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Tabla de Contenidos

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Configuración](#configuración)
- [Uso](#uso)
- [API Endpoints](#api-endpoints)
- [Base de Datos](#base-de-datos)
- [Arquitectura](#arquitectura)
- [Testing](#testing)
- [Contribuir](#contribuir)
- [Licencia](#licencia)

## ✨ Características

### Monitoreo en Tiempo Real
- **OEE (Overall Equipment Effectiveness)**: Métrica compuesta de Disponibilidad × Rendimiento × Calidad
- **Disponibilidad**: Porcentaje de tiempo operativo del equipo
- **Rendimiento**: Velocidad de producción actual vs velocidad teórica
- **Calidad**: Porcentaje de productos sin defectos

### Dashboard Interactivo
- Selector dinámico de equipos
- Tarjetas de resumen de KPIs
- Gráficos en tiempo real con Chart.js
- Interfaz responsiva (móvil, tablet, desktop)
- Actualización automática de datos

### Broadcasting en Tiempo Real
- Notificaciones instantáneas via Pusher
- Eventos de actualización de KPI
- Sincronización en tiempo real entre clientes

### Gestión de Datos
- Modelos completos para Equipment, Production Data, Quality Data, Downtime Data
- Seeders para población de datos de prueba
- Factories para generación de datos
- Migrations versionadas

### API REST Completa
- Endpoints para Equipment, Production Data y KPI
- Autenticación con Sanctum
- Validación de datos
- Respuestas estructuradas

## 🔧 Requisitos

- **PHP**: 8.2+
- **Laravel**: 12.0+
- **Node.js**: 18.0+ (para Vite)
- **Composer**: 2.4+
- **Base de Datos**: MySQL 8.0+ o SQLite
- **Pusher** (opcional): Para notificaciones en tiempo real

## 🚀 Instalación

### Paso 1: Clonar el repositorio

```bash
git clone <repository-url>
cd kpi-dashboard
```

### Paso 2: Instalación automática (recomendado)

```bash
composer run setup
```

Este comando ejecuta:
1. Instala dependencias de PHP
2. Genera archivo `.env` desde `.env.example`
3. Genera clave de aplicación
4. Ejecuta migraciones
5. Instala dependencias de Node.js
6. Compila assets

### Paso 3: Instalación manual

```bash
# Instalar dependencias de PHP
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Instalar dependencias de frontend
npm install

# Compilar assets
npm run build
```

## 📁 Estructura del Proyecto

```
kpi-dashboard/
├── app/
│   ├── Events/                      # Eventos de Broadcasting
│   │   ├── KpiUpdated.php
│   │   └── ProductionDataUpdated.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                 # Controladores de API
│   │   │   │   ├── KpiController.php
│   │   │   │   ├── EquipmentController.php
│   │   │   │   └── ProductionDataController.php
│   │   │   ├── DashboardController.php
│   │   │   └── Controller.php
│   │   └── Requests/                # Form Requests para validación
│   ├── Models/                      # Modelos Eloquent
│   │   ├── Equipment.php
│   │   ├── ProductionData.php
│   │   ├── QualityData.php
│   │   ├── DowntimeData.php
│   │   └── User.php
│   ├── Services/
│   │   └── KpiService.php           # Lógica de cálculo de KPIs
│   ├── Events/
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── ...
├── database/
│   ├── migrations/                  # Migraciones de BD
│   │   ├── create_equipment_table.php
│   │   ├── create_production_data_table.php
│   │   ├── create_quality_data_table.php
│   │   ├── create_downtime_data_table.php
│   │   └── ...
│   ├── seeders/                     # Pobladores de datos
│   │   ├── EquipmentSeeder.php
│   │   ├── ProductionDataSeeder.php
│   │   ├── QualityDataSeeder.php
│   │   ├── DowntimeDataSeeder.php
│   │   └── DatabaseSeeder.php
│   └── factories/
│       └── UserFactory.php
├── resources/
│   ├── css/
│   │   └── app.css                  # Estilos Tailwind
│   ├── js/
│   │   ├── app.js                   # Punto de entrada JS
│   │   └── bootstrap.js             # Configuración de Echo/Pusher
│   └── views/
│       ├── dashboard.blade.php      # Vista principal del dashboard
│       └── welcome.blade.php        # Página de bienvenida
├── routes/
│   ├── api.php                      # Rutas de API
│   ├── web.php                      # Rutas web
│   ├── channels.php                 # Canales de Broadcasting
│   └── console.php                  # Comandos CLI
├── config/
│   ├── app.php
│   ├── database.php
│   ├── broadcasting.php             # Configuración de Pusher
│   └── ...
├── storage/                         # Almacenamiento de la aplicación
├── public/                          # Raíz web
├── tests/                           # Tests unitarios y funcionales
├── bootstrap/                       # Bootstrap de la aplicación
├── vendor/                          # Dependencias de Composer
├── node_modules/                    # Dependencias de npm
├── .env.example                     # Plantilla de variables de entorno
├── artisan                          # Herramienta de línea de comandos
├── composer.json                    # Configuración de Composer
├── package.json                     # Configuración de npm
├── phpunit.xml                      # Configuración de PHPUnit
├── vite.config.js                   # Configuración de Vite
└── README.md                        # Este archivo
```

## ⚙️ Configuración

### Variables de Entorno (`.env`)

```env
# Aplicación
APP_NAME="KPI Dashboard"
APP_ENV=production
APP_KEY=                            # Generar con: php artisan key:generate
APP_DEBUG=false
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kpi_dashboard
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting (Pusher)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

# Queue
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Configurar Pusher (Opcional)

Para habilitar notificaciones en tiempo real:

1. Crear cuenta en [Pusher.com](https://pusher.com)
2. Obtener credenciales (APP_ID, APP_KEY, APP_SECRET, CLUSTER)
3. Actualizar `.env` con las credenciales
4. Configurar Pusher en `resources/js/bootstrap.js`

### Base de Datos

La aplicación utiliza las siguientes tablas:

- **equipment**: Registros de equipos industriales
- **production_data**: Datos de producción por equipo
- **quality_data**: Métricas de calidad
- **downtime_data**: Registros de tiempo de inactividad
- **users**: Usuarios del sistema

## 💻 Uso

### Iniciar Desarrollo

```bash
# Opción 1: Usando el script dev
composer run dev

# Opción 2: Manualmente
php artisan serve              # Inicia servidor en localhost:8000
php artisan queue:listen       # Procesa colas
php artisan pail               # Logs en tiempo real
npm run dev                    # Inicia Vite en modo desarrollo
```

El comando `composer run dev` inicia todos los servicios concurrentemente:
- **Server**: http://localhost:8000
- **Queue Listener**: Procesa trabajos
- **Pail**: Monitoreo de logs
- **Vite**: Compilación de assets

### Acceder al Dashboard

```
http://localhost:8000
```

### Ejecutar Seeders

```bash
# Poblar la base de datos con datos de prueba
php artisan db:seed

# Sembrar solo EquipmentSeeder
php artisan db:seed --class=EquipmentSeeder
```

### Compilar Assets

```bash
# Desarrollo (con hot reload)
npm run dev

# Producción (minificado)
npm run build
```

## 📡 API Endpoints

### Autenticación
```
GET /api/user (requiere auth:sanctum)
```

### Equipment
```
GET    /api/equipment              # Listar todos los equipos
POST   /api/equipment              # Crear nuevo equipo
GET    /api/equipment/{id}         # Obtener equipo específico
PUT    /api/equipment/{id}         # Actualizar equipo
DELETE /api/equipment/{id}         # Eliminar equipo
```

### Production Data
```
GET    /api/production-data        # Listar datos de producción
POST   /api/production-data        # Crear registro
GET    /api/production-data/{id}   # Obtener registro
PUT    /api/production-data/{id}   # Actualizar registro
DELETE /api/production-data/{id}   # Eliminar registro
```

### KPI
```
GET    /api/kpi/                                          # Obtener KPIs de todos los equipos
GET    /api/kpi/{equipmentId}                            # Obtener OEE completo de un equipo
GET    /api/kpi/{equipmentId}/availability               # Obtener disponibilidad
GET    /api/kpi/{equipmentId}/performance                # Obtener rendimiento
GET    /api/kpi/{equipmentId}/quality                    # Obtener calidad
```

### Ejemplo de Respuesta KPI

```json
{
  "oee": 78.45,
  "availability": 95.0,
  "performance": 87.5,
  "quality": 92.1,
  "period": {
    "start": "2025-11-07 00:00:00",
    "end": "2025-11-07 23:59:59"
  }
}
```

## 🗄️ Base de Datos

### Modelo de Datos

#### Equipment
```sql
- id (PK)
- name: string
- code: string (único)
- type: string
- location: string
- is_active: boolean
- created_at, updated_at
```

#### Production Data
```sql
- id (PK)
- equipment_id (FK)
- planned_quantity: decimal
- actual_quantity: decimal
- ideal_cycle_time: decimal
- actual_cycle_time: decimal
- recorded_at: timestamp
- created_at, updated_at
```

#### Quality Data
```sql
- id (PK)
- equipment_id (FK)
- total_pieces: integer
- defective_pieces: integer
- defect_reason: text (nullable)
- recorded_at: timestamp
- created_at, updated_at
```

#### Downtime Data
```sql
- id (PK)
- equipment_id (FK)
- reason: string
- duration_minutes: integer
- started_at: timestamp
- ended_at: timestamp (nullable)
- created_at, updated_at
```

## 🏗️ Arquitectura

### Patrones de Diseño Utilizados

**Service Layer**: La lógica de negocio se centraliza en `KpiService` para:
- Cálculo de OEE
- Cálculo de Disponibilidad
- Cálculo de Rendimiento
- Cálculo de Calidad

**Events & Broadcasting**: Se utilizan eventos de Laravel para:
- Notificaciones en tiempo real
- Sincronización entre clientes
- Actualizaciones de dashboard

**RESTful API**: Endpoints bien definidos siguiendo estándares REST

### Flujo de Datos

```
Dashboard (Blade) 
    ↓
JavaScript (Chart.js)
    ↓
API REST (Controllers)
    ↓
Services (Lógica de negocio)
    ↓
Models (Eloquent ORM)
    ↓
Base de Datos
    ↑
Broadcasting (Pusher)
    ↑
Eventos de Laravel
```

## 🧪 Testing

### Ejecutar Tests

```bash
# Todos los tests
composer test

# Tests específicos
php artisan test --filter=KpiTest

# Con cobertura
php artisan test --coverage
```

### Estructura de Tests

```
tests/
├── Feature/         # Tests de características
│   └── ExampleTest.php
└── Unit/           # Tests unitarios
    └── ExampleTest.php
```

## 🤝 Contribuir

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'feat: add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

### Convenciones de Commits

Seguimos [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: agregar nueva característica
fix: corregir un bug
docs: cambios en documentación
style: cambios de formato (espacios, punto y coma, etc)
refactor: refactorización sin cambiar funcionalidad
perf: mejora de rendimiento
test: agregar o actualizar tests
chore: cambios en build, dependencias, etc
```

## 📦 Dependencias Principales

### Backend (Composer)
- **laravel/framework**: Framework web
- **laravel/sanctum**: Autenticación API
- **pusher/pusher-php-server**: Broadcasting
- **phpunit/phpunit**: Testing
- **laravel/pint**: Code formatting

### Frontend (npm)
- **tailwindcss**: Utilidades CSS
- **laravel-vite-plugin**: Integración Vite-Laravel
- **laravel-echo**: Broadcasting cliente
- **pusher-js**: Cliente de Pusher
- **axios**: Cliente HTTP
- **chart.js**: Gráficos

## 📝 Logs y Debugging

### Monitorear Logs en Tiempo Real

```bash
php artisan pail
```

### Acceder a Tinker (REPL)

```bash
php artisan tinker

# Ejemplo: Obtener todos los equipos
$equipment = App\Models\Equipment::all();

# Calcular KPI de un equipo
$kpiService = app(App\Services\KpiService::class);
$kpi = $kpiService->calculateOEE(1);
dd($kpi);
```

## 🐛 Troubleshooting

### Error: "Application key missing"
```bash
php artisan key:generate
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error de BD
```bash
# Resetear base de datos
php artisan migrate:reset
php artisan migrate

# O con seeders
php artisan migrate:fresh --seed
```

### Assets no se cargan
```bash
# Reconstruir assets
npm run build

# Limpiar caché de Vite
rm -rf node_modules/.vite
npm run dev
```

## 📞 Soporte

Para reportar bugs o solicitar features, abre un issue en el repositorio.

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

---

**Desenvolvido con ❤️ usando Laravel y Tailwind CSS**

Última actualización: 7 de noviembre de 2025
