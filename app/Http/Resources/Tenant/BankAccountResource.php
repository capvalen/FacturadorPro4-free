<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id'                => $this->id,
			'bank_id'           => $this->bank_id,
			'description'       => $this->description,
			'number'            => $this->number,
			'cci'               => $this->cci,
			'currency_type_id'  => $this->currency_type_id,
			'initial_balance'   => $this->initial_balance,
			'show_in_documents' => $this->show_in_documents,
		];
	}
}
