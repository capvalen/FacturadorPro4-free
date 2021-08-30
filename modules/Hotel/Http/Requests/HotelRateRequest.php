<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRateRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'description' => ['required', 'max:50', Rule::unique('tenant.hotel_rates', 'description')->ignore($this->id)],
			'active'      => 'required|boolean',
		];
	}
}
