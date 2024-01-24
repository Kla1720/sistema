<?php

namespace Modules\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountingPlanRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->input('id');
        return [

            'name' => [
                'required',
            ]
        ];

    }
}
