<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRentItem extends ModelTenant
{
	protected $table = 'hotel_rent_items';

	protected $fillable = [
		'type',
		'hotel_rent_id',
		'item_id',
		'item',
		'payment_status',
	];

	public function getItemAttribute($value)
	{
		return (is_null($value)) ? null : (object) json_decode($value);
	}

	public function setItemAttribute($value)
	{
		$this->attributes['item'] = (is_null($value)) ? null : json_encode($value);
	}
}
