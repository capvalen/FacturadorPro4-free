<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelFloor extends ModelTenant
{
	protected $table = 'hotel_floors';

	protected $fillable = ['description', 'active'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
