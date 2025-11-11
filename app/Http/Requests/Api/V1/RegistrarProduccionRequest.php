<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarProduccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // La máquina se autentica vía Sanctum token
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cantidad_producida' => 'required|integer|min:0',
            'cantidad_buena' => 'required|integer|min:0',
            'cantidad_mala' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cantidad_producida.required' => 'La cantidad producida es requerida',
            'cantidad_producida.integer' => 'La cantidad producida debe ser un número',
            'cantidad_buena.required' => 'La cantidad buena es requerida',
            'cantidad_buena.integer' => 'La cantidad buena debe ser un número',
            'cantidad_mala.required' => 'La cantidad mala es requerida',
            'cantidad_mala.integer' => 'La cantidad mala debe ser un número',
        ];
    }
}
