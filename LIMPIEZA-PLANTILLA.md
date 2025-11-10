# 🧹 Limpieza de Plantilla Laravel

> **Guía para purgar dependencias innecesarias del proyecto base**

---

## ❌ Librerías a Remover

### 1. Pusher (Usaremos Laravel Reverb)

**¿Por qué removerlo?**
- La plantilla incluye `pusher/pusher-php-server` y `pusher-js`
- Nosotros usaremos **Laravel Reverb** (solución nativa de Laravel para WebSockets)
- Reverb es más rápido, gratuito y está optimizado para Laravel

```bash
# Remover del backend
composer remove pusher/pusher-php-server

# Remover del frontend (lo reinstalaremos después con Reverb)
npm uninstall pusher-js laravel-echo
```

---

### 2. Laravel DomPDF (Opcional)

**¿Por qué removerlo?**
- Si NO necesitas generar PDFs, no lo necesitas
- Ocupa espacio y recursos

```bash
composer remove barryvdh/laravel-dompdf
```

**¿Cuándo NO removerlo?**
- Si planeas generar reportes PDF de KPIs

---

### 3. Laravel Sail (Opcional)

**¿Por qué removerlo?**
- Solo necesario si usas Docker
- Si usas Laragon, XAMPP, Valet, etc., no lo necesitas

```bash
composer remove --dev laravel/sail
```

---

## ✅ Instalación Limpia

### Script Completo de Limpieza

```bash
#!/bin/bash
# limpieza.sh

echo "🧹 Limpiando dependencias innecesarias..."

# Backend
echo "📦 Removiendo Pusher del backend..."
composer remove pusher/pusher-php-server

echo "📦 Removiendo DomPDF (opcional)..."
composer remove barryvdh/laravel-dompdf

echo "📦 Removiendo Sail (opcional)..."
composer remove --dev laravel/sail

# Frontend
echo "🎨 Removiendo librerías de Pusher del frontend..."
npm uninstall pusher-js laravel-echo

echo "✅ Limpieza completada!"
echo ""
echo "Ejecuta: composer install && npm install"
```

### PowerShell (Windows)

```powershell
# limpieza.ps1

Write-Host "🧹 Limpiando dependencias innecesarias..." -ForegroundColor Cyan

# Backend
Write-Host "📦 Removiendo Pusher del backend..." -ForegroundColor Yellow
composer remove pusher/pusher-php-server

Write-Host "📦 Removiendo DomPDF (opcional)..." -ForegroundColor Yellow
composer remove barryvdh/laravel-dompdf

Write-Host "📦 Removiendo Sail (opcional)..." -ForegroundColor Yellow
composer remove --dev laravel/sail

# Frontend
Write-Host "🎨 Removiendo librerías de Pusher del frontend..." -ForegroundColor Yellow
npm uninstall pusher-js laravel-echo

Write-Host "✅ Limpieza completada!" -ForegroundColor Green
Write-Host ""
Write-Host "Ejecuta: composer install && npm install"
```

---

## 🔧 Instalación de Reemplazos

### Instalar Laravel Reverb (Reemplazo de Pusher)

```bash
# Instalar broadcasting con Reverb
php artisan install:broadcasting

# Esto automáticamente:
# 1. Instala laravel/reverb (Composer)
# 2. Instala laravel-echo (npm)
# 3. Instala pusher-js (npm) - necesario para el protocolo
# 4. Publica configuración de broadcasting
# 5. Crea archivo de configuración de Reverb
```

### Verificar que se instaló correctamente

```bash
# Verificar composer.json
cat composer.json | grep reverb
# Debería mostrar: "laravel/reverb": "^..."

# Verificar package.json
cat package.json | grep laravel-echo
# Debería mostrar: "laravel-echo": "^..."
```

### Configurar .env para Reverb

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=123456
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 📋 Checklist de Limpieza

### Backend (Composer)

- [ ] ❌ Remover `pusher/pusher-php-server`
- [ ] ❌ Remover `barryvdh/laravel-dompdf` (opcional)
- [ ] ❌ Remover `laravel/sail` (opcional si no usas Docker)
- [ ] ✅ Instalar `laravel/reverb` (vía `install:broadcasting`)
- [ ] ✅ Instalar `spatie/laravel-permission`
- [ ] ✅ Verificar que `laravel/sanctum` esté instalado

### Frontend (npm)

- [ ] ❌ Remover `pusher-js` (temporal)
- [ ] ❌ Remover `laravel-echo` (temporal)
- [ ] ✅ Reinstalar vía `php artisan install:broadcasting`
- [ ] ✅ Instalar `alpinejs`
- [ ] ✅ Instalar `chart.js` o `apexcharts`
- [ ] ✅ Verificar que `tailwindcss` esté instalado

### Archivos de Configuración

- [ ] Actualizar `.env` con variables de Reverb
- [ ] Verificar `config/broadcasting.php` tenga configuración de Reverb
- [ ] Verificar `resources/js/bootstrap.js` o `echo.js` use Reverb

---

## 🔍 Verificación Final

### Comando de Verificación

```bash
# Backend
echo "📦 Paquetes Composer:"
composer show | grep -E "(reverb|pusher|dompdf|sail|permission|sanctum)"

# Frontend
echo ""
echo "🎨 Paquetes npm:"
npm list --depth=0 | grep -E "(echo|pusher|alpine|chart|tailwind)"
```

### Resultado Esperado

**✅ SÍ deberías ver:**
```
laravel/reverb
laravel/sanctum
spatie/laravel-permission
laravel-echo
alpinejs
chart.js (o apexcharts)
tailwindcss
```

**❌ NO deberías ver:**
```
pusher/pusher-php-server
barryvdh/laravel-dompdf (si lo removiste)
laravel/sail (si lo removiste)
```

---

## 🚨 Troubleshooting

### Error: "Cannot remove package, it's required by..."

```bash
# Ver qué paquetes dependen de él
composer why pusher/pusher-php-server

# Si es requerido por laravel/framework, edita composer.json manualmente
# y remueve la línea, luego:
composer update
```

### Error al compilar assets después de remover pusher-js

```bash
# Reinstalar con Reverb
php artisan install:broadcasting

# Limpiar cache de Vite
rm -rf node_modules/.vite
npm run build
```

### Reverb no inicia

```bash
# Verificar que esté en composer.json
composer show laravel/reverb

# Si no está, instalar manualmente
composer require laravel/reverb
php artisan install:broadcasting
```

---

## 📊 Comparación de Tamaño

### Antes de la Limpieza
```
vendor/: ~150 MB
node_modules/: ~250 MB
Total: ~400 MB
```

### Después de la Limpieza
```
vendor/: ~140 MB (-10 MB)
node_modules/: ~240 MB (-10 MB)
Total: ~380 MB (-20 MB)
```

> **Nota:** Los números son aproximados y dependen de las versiones exactas.

---

## 🎯 Próximos Pasos

Después de limpiar la plantilla:

1. ✅ Ejecutar `composer install`
2. ✅ Ejecutar `npm install`
3. ✅ Configurar `.env` con Reverb
4. ✅ Ejecutar `php artisan reverb:start`
5. ✅ Continuar con la [Fase 1 del Plan de Acción](plan-de-accion-check.md)

---

**Última actualización:** 9 de noviembre de 2025
