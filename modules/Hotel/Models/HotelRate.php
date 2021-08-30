<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRate extends ModelTenant
{
	protected $table = 'hotel_rates';

	protected $fillable = ['description', 'active'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
