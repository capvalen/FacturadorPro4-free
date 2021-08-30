<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DispatchCollection extends ResourceCollection
{
	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function toArray($request)
	{
		return $this->collection->transform(function ($row, $key) {
			$has_cdr = false;

			if (in_array($row->state_type_id, ['05', '07'])) {
				$has_cdr = true;
			}

			return [
				'id'                     => $row->id,
				'external_id'            => $row->external_id,
				'group_id'               => $row->group_id,
				'soap_type_id'           => $row->soap_type_id,
				'date_of_issue'          => $row->date_of_issue->format('Y-m-d'),
				'number'                 => $row->number_full,
				'customer_id'            => $row->customer_id,
				'customer_name'          => $row->customer->name,
				'customer_number'        => $row->customer->identity_document_type->description . ' ' . $row->customer->number,
				'date_of_shipping'       => $row->date_of_shipping->format('Y-m-d'),
				'state_type_id'          => $row->state_type_id,
				'state_type_description' => $row->state_type->description,
				'has_xml'                => $row->has_xml,
				'has_pdf'                => $row->has_pdf,
				// 'has_cdr' => $row->has_cdr,
				'has_cdr'               => $has_cdr,
				'download_external_xml' => $row->download_external_xml,
				'download_external_pdf' => $row->download_external_pdf,
				'download_external_cdr' => $row->download_external_cdr,
                'reference_document_id' => $row->reference_document_id,
				'created_at'            => $row->created_at->format('Y-m-d H:i:s'),
				'updated_at'            => $row->updated_at->format('Y-m-d H:i:s'),
			];
		});
	}
}
