<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRoomRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'name'              => ['required', 'max:25', Rule::unique('tenant.hotel_rooms', 'name')->ignore($this->id)],
			'hotel_category_id' => ['required', 'numeric', 'exists:tenant.hotel_categories,id'],
			'hotel_floor_id'    => ['required', 'numeric', 'exists:tenant.hotel_floors,id'],
			'description'       => 'nullable|max:250',
			'active'            => 'required|boolean',
		];
	}
}
