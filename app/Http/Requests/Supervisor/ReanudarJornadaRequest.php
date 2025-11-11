<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ReanudarJornadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        if (!$user instanceof User) {
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
        ];
    }
}
