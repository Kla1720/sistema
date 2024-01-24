<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransporteChoferRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'dni' => ['required', 'max:8', Rule::unique('tenant.transporte_choferes', 'dni')->ignore($this->id)],
            'licencia' => ['required', 'max:10'],
		];
	}
}
