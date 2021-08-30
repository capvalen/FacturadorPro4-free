<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRentRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'customer_id'              => 'required|numeric',
			'customer'                 => 'required',
			'customer.name'            => 'required',
			'customer.address'         => 'required',
			'notes'                    => 'max:250',
			'towels'                   => 'required|numeric|min:1',
			'duration'                 => 'required|numeric|min:1',
			'quantity_persons'         => 'required|numeric|min:1',
			'payment_status'           => 'required|in:PAID,DEBT',
			'output_date'              => 'required|date_format:Y-m-d',
			'output_time'              => 'required|date_format:H:i',
			'product'                  => 'required'
		];
	}
}
