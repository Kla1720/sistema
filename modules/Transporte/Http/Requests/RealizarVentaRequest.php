<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RealizarVentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'cliente_id'=> ['required'],
            'estado_asiento_id' => ['required'],
            'fecha_salida' => ['required'],
            // 'pasajero_id' => ['required'],
            // 'programacion_id' => ['required'],
            'destino_id' => ['required'],
            'numero_asiento' => ['required'],
            'hora_salida' => ['required']
        ];
    }
}
