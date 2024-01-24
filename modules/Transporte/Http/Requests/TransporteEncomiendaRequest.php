<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransporteEncomiendaRequest extends FormRequest
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
            //
            'remitente_id' => ['required'],
            //'destinatario_id' => ['required'],
            'estado_pago_id' => ['required'],
            'estado_envio_id' => ['required'],
            'destino_id' => ['required']
            // 'programacion_id' => ['required','exists:tenant.transporte_programaciones,id']
        ];
    }
}
