<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelFloorRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'description' => ['required', 'max:50', Rule::unique('tenant.hotel_floors', 'description')->ignore($this->id)],
			'active'      => 'required|boolean',
		];
	}
}
