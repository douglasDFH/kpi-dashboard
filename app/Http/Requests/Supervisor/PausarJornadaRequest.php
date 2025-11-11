<?php

namespace App\Http\Requests\Supervisor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PausarJornadaRequest extends FormRequest
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
            'jornada_id' => [
                'required',
                'uuid',
                'exists:jornadas_produccion,id',
            ],
            'motivo' => [
                'required',
                'string',
                'max:255',
                'in:Cambio de turno,Fallo de máquina,Falta de materia prima,Mantenimiento,Limpieza,Otro',
            ],
            'duracion_estimada' => [
                'nullable',
                'integer',
                'min:1',
                'max:1440', // 24 horas en minutos
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'jornada_id.required' => 'La jornada es obligatoria.',
            'jornada_id.uuid' => 'El ID de jornada debe ser un UUID válido.',
            'jornada_id.exists' => 'La jornada seleccionada no existe.',
            'motivo.required' => 'El motivo de la parada es obligatorio.',
            'motivo.in' => 'El motivo seleccionado no es válido.',
            'duracion_estimada.max' => 'La duración estimada no puede exceder 24 horas.',
        ];
    }
}
