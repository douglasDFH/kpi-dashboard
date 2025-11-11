<?php

namespace App\Http\Requests\Mantenimiento;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegistrarMantenimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return false;
        }

        return $user->hasRole('supervisor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'maquina_id' => [
                'required',
                'uuid',
                'exists:maquinas,id',
            ],
            'tipo' => [
                'required',
                'string',
                'in:preventivo,correctivo,predictivo',
            ],
            'descripcion' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'duracion_minutos' => [
                'nullable',
                'integer',
                'min:1',
                'max:1440', // 24 horas
            ],
            'piezas_reemplazadas' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'maquina_id.required' => 'La máquina es obligatoria.',
            'maquina_id.exists' => 'La máquina seleccionada no existe.',
            'tipo.required' => 'El tipo de mantenimiento es obligatorio.',
            'tipo.in' => 'El tipo debe ser: preventivo, correctivo o predictivo.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'duracion_minutos.max' => 'La duración no puede exceder 24 horas.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'supervisor_id' => Auth::id(),
        ]);
    }
}
