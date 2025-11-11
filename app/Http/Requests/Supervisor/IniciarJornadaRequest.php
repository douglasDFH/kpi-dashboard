<?php

namespace App\Http\Requests\Supervisor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IniciarJornadaRequest extends FormRequest
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
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'maquina_id.required' => 'La máquina es obligatoria.',
            'maquina_id.uuid' => 'El ID de máquina debe ser un UUID válido.',
            'maquina_id.exists' => 'La máquina seleccionada no existe.',
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
