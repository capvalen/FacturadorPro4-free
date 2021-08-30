<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelCategory extends ModelTenant
{
	protected $table = 'hotel_categories';

	protected $fillable = ['description', 'active'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
