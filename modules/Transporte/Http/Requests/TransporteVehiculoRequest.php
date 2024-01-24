<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransporteVehiculoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'placa'    => ['required', 'max:7', Rule::unique('tenant.transporte_vehiculos', 'placa')->ignore($this->id)],
            'nombre'   => ['required', 'string'],
            // 'asientos' => ['required', 'numeric'],
            'pisos'    => 'integer|max:2',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
