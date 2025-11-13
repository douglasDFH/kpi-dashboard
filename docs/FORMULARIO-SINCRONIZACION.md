# 🔄 Formulario de Registro con Sincronización Automática

## 📋 Cambios Implementados

### ✅ Características Nuevas

#### 1. **Pre-llenado Automático del Formulario**
El formulario "Registrar Producción" ahora se **pre-llena automáticamente** con los valores actuales de la simulación en tiempo real.

**ANTES:**
- Formulario vacío (0, 0, 0)
- Solo se pre-llenaba al alcanzar 100% (pending_registration)

**DESPUÉS:**
- Formulario siempre sincronizado con la simulación
- Se actualiza cada 5 segundos automáticamente
- Valores actuales visibles todo el tiempo

#### 2. **Toggle de Sincronización Automática**
Se agregó un interruptor (toggle) para controlar la sincronización:

```
┌─────────────────────────────────────────────┐
│  🔄 Sincronización Automática          ON   │
│  Los campos se actualizan con la simulación │
└─────────────────────────────────────────────┘
```

**Funcionalidad:**
- **ON (Activado)**: Los campos se actualizan automáticamente cada 5s
- **OFF (Desactivado)**: El usuario puede editar manualmente los valores

---

## 🎯 Flujo de Uso

### Escenario 1: Sincronización Automática (Toggle ON)

```
1. Crear jornada → Status: active
2. El formulario muestra valores iniciales (0, 0, 0)
3. La simulación comienza:
   - Segundo 5: Formulario → (5, 5, 0)
   - Segundo 10: Formulario → (12, 11, 1)
   - Segundo 15: Formulario → (18, 17, 1)
   - ...continúa hasta 100%
4. Al llegar a 100%:
   - Status → pending_registration
   - Toggle se desactiva automáticamente
   - Campos se vuelven solo lectura
5. Usuario hace clic en "Registrar"
6. Jornada finalizada ✅
```

### Escenario 2: Edición Manual (Toggle OFF)

```
1. Jornada activa con simulación corriendo
2. Usuario desactiva el toggle de sincronización
3. Los campos dejan de actualizarse
4. Usuario puede editar manualmente:
   - Cantidad Total: 150
   - Unidades Buenas: 145
   - Unidades Defectuosas: 5
5. Validación automática: 145 + 5 = 150 ✅
6. Usuario hace clic en "Registrar"
7. Se registra la producción manual
```

---

## 💻 Implementación Técnica

### 1. **Inicialización del Formulario**

```javascript
// Alpine.js data
form: {
    quantity: {{ $shift->actual_production }},      // Pre-lleno inicial
    good_units: {{ $shift->good_units }},           // Pre-lleno inicial
    defective_units: {{ $shift->defective_units }}  // Pre-lleno inicial
},
autoSync: true, // Toggle activado por defecto
```

### 2. **Toggle HTML**

```html
<div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
    <label class="flex items-center justify-between cursor-pointer">
        <div class="flex items-center">
            <svg>...</svg>
            <div>
                <span class="text-sm font-medium text-blue-800">
                    Sincronización Automática
                </span>
                <p class="text-xs text-blue-600 mt-0.5">
                    Los campos se actualizan con la simulación
                </p>
            </div>
        </div>
        <div class="relative">
            <input type="checkbox" x-model="autoSync" checked>
            <div class="w-11 h-6 bg-gray-200 ... peer-checked:bg-blue-600"></div>
        </div>
    </label>
</div>
```

### 3. **Campos con Binding Condicional**

```html
<input 
    type="number" 
    x-model="form.quantity"
    :readonly="autoSync || '{{ $shift->status }}' === 'pending_registration'"
    :class="{
        'bg-gray-100': autoSync || '{{ $shift->status }}' === 'pending_registration',
        'bg-white': !autoSync && '{{ $shift->status }}' !== 'pending_registration'
    }"
    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
    required>
```

**Lógica:**
- Si `autoSync === true` → Campo readonly (fondo gris)
- Si `autoSync === false` → Campo editable (fondo blanco)
- Si `status === 'pending_registration'` → Siempre readonly

### 4. **Actualización en Tiempo Real**

```javascript
listenForUpdates() {
    window.Echo.channel('work-shift.{{ $shift->id }}')
        .listen('.production.updated', (e) => {
            // Actualizar datos de las tarjetas
            this.actualProduction = e.actual_production;
            this.goodUnits = e.good_units;
            this.defectiveUnits = e.defective_units;
            
            // Actualizar formulario SOLO si autoSync está activado
            if (this.autoSync) {
                this.form.quantity = e.actual_production;
                this.form.good_units = e.good_units;
                this.form.defective_units = e.defective_units;
            }
            
            // Actualizar gráfico siempre
            this.productionChart.update();
        });
}
```

---

## 🎨 Estados Visuales

### Toggle ON (Sincronización Activa)
```
┌────────────────────────────────────┐
│ 🔄 Sincronización Automática  [ON] │
├────────────────────────────────────┤
│ Cantidad Total *                   │
│ ┌────────────────┐                 │
│ │ 150 (readonly) │ ← Gris claro    │
│ └────────────────┘                 │
│                                    │
│ Unidades Buenas *                  │
│ ┌────────────────┐                 │
│ │ 143 (readonly) │ ← Gris claro    │
│ └────────────────┘                 │
│                                    │
│ Unidades Defectuosas *             │
│ ┌────────────────┐                 │
│ │ 7 (readonly)   │ ← Gris claro    │
│ └────────────────┘                 │
│                                    │
│ ✅ Los valores son correctos       │
│                                    │
│ [✅ Registrar Producción]          │
└────────────────────────────────────┘
```

### Toggle OFF (Edición Manual)
```
┌────────────────────────────────────┐
│ 🔄 Sincronización Automática [OFF] │
├────────────────────────────────────┤
│ Cantidad Total *                   │
│ ┌────────────────┐                 │
│ │ 200 (editable) │ ← Blanco        │
│ └────────────────┘                 │
│                                    │
│ Unidades Buenas *                  │
│ ┌────────────────┐                 │
│ │ 195 (editable) │ ← Blanco        │
│ └────────────────┘                 │
│                                    │
│ Unidades Defectuosas *             │
│ ┌────────────────┐                 │
│ │ 5 (editable)   │ ← Blanco        │
│ └────────────────┘                 │
│                                    │
│ ✅ Los valores son correctos       │
│                                    │
│ [✅ Registrar Producción]          │
└────────────────────────────────────┘
```

---

## 📐 Casos de Uso

### Caso 1: Supervisión Pasiva
**Usuario:** Supervisor que solo observa
**Acción:** Dejar toggle ON
**Resultado:** Ve la producción en tiempo real, puede registrar cuando quiera

### Caso 2: Corrección Manual
**Usuario:** Operador que detecta un error
**Acción:** Desactivar toggle, corregir valores
**Resultado:** Registra los valores correctos manualmente

### Caso 3: Registro Inmediato
**Usuario:** Operador que quiere registrar parcialmente
**Acción:** Puede registrar en cualquier momento
**Resultado:** Se registra el estado actual y continúa la simulación

### Caso 4: Finalización al 100%
**Usuario:** Cualquiera
**Acción:** Esperar a que llegue al 100%
**Resultado:** Campos se pre-llenan, toggle se desactiva, confirma y finaliza

---

## 🔍 Validaciones

### Validación de Consistencia
```javascript
get isFormValid() {
    return this.form.quantity > 0 && 
           this.form.quantity === (this.form.good_units + this.form.defective_units);
}
```

### Feedback Visual
```html
<div class="p-3 rounded-lg" :class="isFormValid ? 'bg-green-50' : 'bg-gray-50'">
    <p class="text-xs" :class="isFormValid ? 'text-green-700' : 'text-gray-600'">
        <strong>Validación:</strong><br>
        Total = Buenas + Defectuosas<br>
        <span x-text="form.quantity"></span> = 
        <span x-text="form.good_units"></span> + 
        <span x-text="form.defective_units"></span>
        <span x-show="!isFormValid" class="text-red-600 block mt-1">
            ⚠️ Los valores no coinciden
        </span>
        <span x-show="isFormValid" class="text-green-600 block mt-1">
            ✅ Los valores son correctos
        </span>
    </p>
</div>
```

---

## ✅ Ventajas

1. **Transparencia Total**: El usuario ve exactamente lo que se va a registrar
2. **Flexibilidad**: Puede optar por sincronización automática o manual
3. **Prevención de Errores**: Validación en tiempo real
4. **Mejor UX**: Feedback visual inmediato
5. **Trazabilidad**: Siempre sabe qué valores se están produciendo

---

## 🧪 Cómo Probar

1. **Iniciar servicios:**
```bash
php artisan serve
php artisan reverb:start
php artisan queue:work
```

2. **Crear plan y jornada:**
```
Plan: http://127.0.0.1:8000/production-plans/create
Jornada: http://127.0.0.1:8000/work-shifts/create
```

3. **Ver jornada en tiempo real:**
```
http://127.0.0.1:8000/work-shifts/{id}
```

4. **Observar el formulario:**
   - ✅ Toggle está ON por defecto
   - ✅ Campos están en gris (readonly)
   - ✅ Valores se actualizan cada 5s
   - ✅ Gráfico se actualiza simultáneamente

5. **Probar edición manual:**
   - Desactivar toggle → OFF
   - Campos se vuelven blancos (editables)
   - Modificar valores
   - Verificar validación
   - Registrar

6. **Probar finalización al 100%:**
   - Esperar a que llegue a 100%
   - Status cambia a "pending_registration"
   - Toggle desaparece
   - Campos prellenados y readonly
   - Confirmar

---

## 📝 Notas Importantes

- El toggle **solo aparece** cuando status = 'active'
- En status = 'pending_registration', los campos son siempre readonly
- La sincronización no afecta el gráfico (siempre se actualiza)
- Los valores de las tarjetas superiores siempre se actualizan
- Al desactivar el toggle, la simulación sigue corriendo en background

---

**Fecha de implementación:** 11 de noviembre de 2025  
**Archivo modificado:** `resources/views/work-shifts/show.blade.php`
