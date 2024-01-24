<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManifiestoFormRequest extends FormRequest
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
            'serie' => ['required'],
            'fecha' => ['required'],
            'programacion_id' => ['required'],
            'hora' => ['required'],
            'tipo' => ['required'],
            'chofer_id' => ['required'],
            'copiloto_id' => ['required']
            //
        ];
    }
}
