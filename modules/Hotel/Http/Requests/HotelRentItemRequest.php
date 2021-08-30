<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRentItemRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'products'                  => 'required|array',
			'products.*.payment_status' => 'required|in:PAID,DEBT',
		];
	}
}
