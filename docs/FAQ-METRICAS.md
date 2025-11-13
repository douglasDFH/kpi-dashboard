# ❓ Preguntas Frecuentes - Métricas de Producción

## 📊 Sobre las Métricas

### ¿Cuál es la diferencia entre "Producción Planificada" y "Producción Real"?

- **Producción Planificada**: Es el valor objetivo definido en el Plan de Producción (campo `target_quantity`). Este valor se copia al `target_snapshot` cuando se inicia la jornada y **permanece fijo** durante toda la jornada.

- **Producción Real**: Es la cantidad de unidades efectivamente producidas durante la jornada (campo `actual_production`). Este valor **se actualiza en tiempo real** cada 5 segundos durante la simulación.

**Ejemplo:**
```
Plan #50: target_quantity = 1000 (Producción Planificada)
Jornada #36:
  - Minuto 0: actual_production = 0
  - Minuto 5: actual_production = 50
  - Minuto 10: actual_production = 98
  - ...
  - Completado: actual_production = 1000 (Producción Real final)
```

---

### ¿Cómo se calcula la Eficiencia de Producción?

```
Eficiencia = (Producción Real / Producción Planificada) × 100
```

**Ejemplo:**
- Producción Planificada: 1000 unidades
- Producción Real: 950 unidades
- Eficiencia: (950 / 1000) × 100 = **95%**

**Interpretación:**
- 100% o más: Cumplimiento total u sobre cumplimiento
- 90-99%: Buen rendimiento, cerca del objetivo
- 75-89%: Rendimiento aceptable pero por debajo
- <75%: Rendimiento bajo, requiere revisión

---

### ¿Cómo se garantiza que Unidades Buenas + Defectuosas = Producción Real?

El sistema implementa la validación en **tres niveles**:

#### 1. **Simulación Automática** (`SimulateProduction.php`)
```php
$newProduction = 100;  // Producción Real
$goodUnits = round($newProduction * 0.95);  // 95 unidades
$defectiveUnits = $newProduction - $goodUnits;  // 5 unidades

// Garantiza: 95 + 5 = 100 ✅
```

#### 2. **Validación en Controlador** (`WorkShiftController.php`)
```php
if ($quantity != ($good_units + $defective_units)) {
    return error('Los valores no coinciden');
}
```

#### 3. **Validación en Frontend** (`show.blade.php`)
```javascript
get isFormValid() {
    return this.form.quantity === 
           (this.form.good_units + this.form.defective_units);
}
```

---

### ¿Por qué la simulación usa 95% buenas y 5% defectuosas?

Estos valores son **realistas para industria manufacturera**:
- **95% de calidad** es un estándar común en producción
- **5% de defectos** representa un nivel aceptable de pérdida

Puedes ajustar estos valores en `app/Jobs/SimulateProduction.php`:
```php
// Cambiar de 95/5 a 98/2
$goodUnits = round($newProduction * 0.98);  // 98% buenas
$defectiveUnits = $newProduction - $goodUnits;  // 2% defectuosas
```

---

## 🔄 Sobre la Simulación en Tiempo Real

### ¿Cómo funciona la simulación automática?

1. **Inicio**: Al crear una jornada, se despacha el job `SimulateProduction`
2. **Incremento**: Cada 5 segundos añade entre 1-5 unidades aleatoriamente
3. **Cálculo**: Recalcula buenas/defectuosas sobre el total acumulado
4. **Broadcast**: Envía actualización vía WebSocket
5. **Repetición**: Se auto-programa hasta llegar al 100%

**Código simplificado:**
```php
public function handle(): void
{
    $increment = rand(1, 5);  // Aleatorio 1-5
    $newProduction = $this->actual_production + $increment;
    
    // Calcular
    $goodUnits = round($newProduction * 0.95);
    $defectiveUnits = $newProduction - $goodUnits;
    
    // Guardar
    $this->workShift->update([...]);
    
    // Broadcast
    broadcast(new ProductionUpdated($this->workShift));
    
    // Siguiente ciclo en 5 segundos
    if ($newProduction < $targetQuantity) {
        dispatch(new SimulateProduction($this->workShift))
            ->delay(now()->addSeconds(5));
    }
}
```

---

### ¿Puedo cambiar la velocidad de simulación?

Sí, en `app/Jobs/SimulateProduction.php` busca esta línea:

```php
->delay(now()->addSeconds(5));  // Cambiar el 5 por otro valor
```

**Ejemplos:**
- Más rápido: `->delay(now()->addSeconds(2))`
- Más lento: `->delay(now()->addSeconds(10))`
- Inmediato: `->delay(now()->addSeconds(1))`

---

### ¿Qué pasa si el worker de cola no está corriendo?

La simulación **NO funcionará** porque los jobs no se procesarán.

**Síntomas:**
- La producción no avanza
- El gráfico no se actualiza
- Status permanece en "active" indefinidamente

**Solución:**
```bash
php artisan queue:work
```

**Verificar que funcione:**
```bash
# Ver los jobs en cola
php artisan queue:listen --verbose

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 🌐 Sobre WebSockets

### ¿Para qué sirve Laravel Reverb/Pusher?

Para enviar actualizaciones **en tiempo real** del servidor al navegador sin necesidad de recargar la página.

**Sin WebSockets:**
- Usuario debe recargar página manualmente
- Polling constante (consultas repetidas al servidor)
- Consume más recursos

**Con WebSockets:**
- Actualizaciones instantáneas automáticas
- Conexión bidireccional eficiente
- Mejor experiencia de usuario

---

### ¿Cómo verifico que WebSockets funciona?

1. **Abrir consola del navegador** (F12)
2. **Ir a la jornada activa**
3. **Buscar en la consola:**
   ```javascript
   👂 Escuchando actualizaciones en tiempo real...
   📡 Actualización recibida: {...}
   ```

Si ves estos mensajes, **funciona correctamente**.

Si NO ves los mensajes:
- Verificar que `php artisan reverb:start` esté corriendo
- Verificar `.env`: `BROADCAST_DRIVER=reverb`
- Verificar que no haya errores en consola

---

### ¿Puedo usar Pusher en lugar de Reverb?

Sí, edita `.env`:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=tu_app_id
PUSHER_APP_KEY=tu_key
PUSHER_APP_SECRET=tu_secret
PUSHER_APP_CLUSTER=tu_cluster
```

Luego reinicia el servidor:
```bash
php artisan config:clear
php artisan serve
```

---

## 🎨 Sobre la Interfaz

### ¿Cómo cambio los colores de las métricas?

En `resources/views/work-shifts/show.blade.php`, busca las clases de Tailwind:

**Para Eficiencia:**
```javascript
:class="{
    'text-green-600': productionEfficiency >= 100,
    'text-blue-600': productionEfficiency >= 90,
    'text-yellow-600': productionEfficiency >= 75,
    'text-red-600': productionEfficiency < 75
}"
```

**Cambiar umbrales:**
```javascript
:class="{
    'text-green-600': productionEfficiency >= 95,  // Antes 100
    'text-blue-600': productionEfficiency >= 85,   // Antes 90
    // ...
}"
```

---

### ¿Puedo agregar más métricas?

Sí, sigue este patrón:

#### 1. Agregar getter en modelo
```php
// app/Models/WorkShift.php
public function getTiempoCicloAttribute(): float
{
    $duration = $this->start_time->diffInMinutes($this->end_time ?? now());
    return $this->actual_production > 0 
        ? $duration / $this->actual_production 
        : 0;
}
```

#### 2. Agregar en evento
```php
// app/Events/ProductionUpdated.php
public function broadcastWith(): array
{
    return [
        // ... existentes
        'tiempo_ciclo' => $this->workShift->tiempo_ciclo,
    ];
}
```

#### 3. Agregar en frontend
```javascript
// resources/views/work-shifts/show.blade.php
tiempoCiclo: {{ $shift->tiempo_ciclo ?? 0 }},

get tiempoCicloFormatted() {
    return this.tiempoCiclo.toFixed(2) + ' min/unidad';
}
```

#### 4. Agregar tarjeta
```html
<div class="bg-white rounded-lg shadow-md p-6">
    <p class="text-sm text-gray-600">Tiempo de Ciclo</p>
    <p class="text-3xl font-bold" x-text="tiempoCicloFormatted"></p>
</div>
```

---

## 🐛 Solución de Problemas

### El gráfico no se muestra

**Causa posible:** Chart.js no cargó correctamente.

**Solución:**
1. Abrir consola del navegador (F12)
2. Verificar errores de red
3. Verificar que aparezca:
   ```
   Chart.js disponible: true
   ```

Si muestra `false`:
- Verificar CDN en `show.blade.php`
- Revisar conexión a internet

---

### Los datos no coinciden

**Síntoma:** `good_units + defective_units ≠ actual_production`

**Causas posibles:**
1. Modificación manual incorrecta en BD
2. Job con error (no completó actualización)
3. Uso de `increment()` sin recalcular

**Solución:**
```sql
-- Recalcular manualmente
UPDATE work_shifts 
SET 
    good_units = ROUND(actual_production * 0.95),
    defective_units = actual_production - ROUND(actual_production * 0.95)
WHERE id = 36;
```

---

### La eficiencia muestra más del 100%

**Esto es correcto!** Significa que se produjo **más de lo planificado**.

**Ejemplo:**
- Producción Planificada: 1000
- Producción Real: 1050
- Eficiencia: 105% ✅ (sobre cumplimiento)

Esto se muestra en **verde** como indicador positivo.

---

### La jornada no avanza de "pending_registration"

**Causa:** Usuario no ha confirmado la producción.

**Solución:**
1. Ir a la jornada: `http://127.0.0.1:8000/work-shifts/36`
2. Verificar datos precargados en formulario
3. Hacer clic en "Registrar Producción"

Si el botón no funciona:
- Abrir consola del navegador
- Buscar errores JavaScript
- Verificar que CSRF token sea válido

---

## 📚 Más Información

### ¿Dónde encuentro más documentación?

1. **`RESUMEN-CAMBIOS.md`**: Resumen ejecutivo de cambios
2. **`docs/CAMBIOS-METRICAS-PRODUCCION.md`**: Documentación técnica completa
3. **`docs/FLUJO-PRODUCCION-TIEMPO-REAL.md`**: Diagramas y flujos visuales
4. **Este archivo**: Preguntas frecuentes

### ¿Cómo reporto un problema?

1. Verificar logs: `storage/logs/laravel.log`
2. Verificar consola del navegador (F12)
3. Documentar el problema con:
   - Pasos para reproducir
   - Mensaje de error exacto
   - Captura de pantalla
   - Valores en la base de datos

### ¿Puedo contribuir mejoras?

¡Claro! Algunas ideas:

1. **Pausar/reanudar** simulación sin finalizar
2. **Ajustar velocidad** de simulación desde UI
3. **Alertas automáticas** cuando métricas están bajas
4. **Export a PDF** con resumen de jornada
5. **Dashboard agregado** con múltiples jornadas
6. **Gráficos históricos** con comparativas

---

**¿Más preguntas?**  
Revisa el código fuente con los comentarios incluidos o consulta la documentación completa en la carpeta `docs/`.

---

**Última actualización:** 11 de noviembre de 2025
