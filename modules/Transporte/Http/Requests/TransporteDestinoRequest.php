<?php

namespace Modules\Transporte\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransporteDestinoRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'nombre'      => ['required', 'max:25', Rule::unique('tenant.transporte_destinos', 'nombre')->ignore($this->id)],
		];
	}
}
