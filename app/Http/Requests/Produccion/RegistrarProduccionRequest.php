<?php

namespace App\Http\Requests\Produccion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegistrarProduccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            'cantidad_producida' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
            'cantidad_buena' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
                'lte:cantidad_producida',
            ],
            'cantidad_mala' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
            'tiempo_ciclo_segundos' => [
                'nullable',
                'integer',
                'min:1',
                'max:3600',
            ],
            'observaciones' => [
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
            'jornada_id.required' => 'La jornada es obligatoria.',
            'jornada_id.exists' => 'La jornada seleccionada no existe.',
            'cantidad_producida.required' => 'La cantidad producida es obligatoria.',
            'cantidad_producida.min' => 'La cantidad debe ser mayor a 0.',
            'cantidad_buena.required' => 'La cantidad de unidades buenas es obligatoria.',
            'cantidad_buena.lte' => 'Las unidades buenas no pueden exceder la producción total.',
            'cantidad_mala.required' => 'La cantidad de unidades malas es obligatoria.',
            'tiempo_ciclo_segundos.max' => 'El tiempo de ciclo no puede exceder 1 hora.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Calcular cantidad_mala si no se proporciona
        if (!$this->has('cantidad_mala') && $this->has('cantidad_producida') && $this->has('cantidad_buena')) {
            $this->merge([
                'cantidad_mala' => $this->cantidad_producida - $this->cantidad_buena,
            ]);
        }
    }
}
