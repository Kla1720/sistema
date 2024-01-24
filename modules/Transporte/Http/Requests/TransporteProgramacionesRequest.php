<?php


namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransporteProgramacionesRequest extends FormRequest
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
            'programacion.terminal_origen_id' => ['required','exists:tenant.transporte_terminales,id'],
            'programacion.terminal_destino_id' => ['required','exists:tenant.transporte_terminales,id'],
            // 'tiempo_aproximado' => ['required'],
            'programacion.vehiculo_id' => ['required','exists:tenant.transporte_vehiculos,id'],
            'programacion.hora_salida' => ['required']
            
            //
        ];
    }
}
