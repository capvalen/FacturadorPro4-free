<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRoomRate extends ModelTenant
{
	protected $table = 'hotel_room_rates';

	protected $fillable = ['hotel_room_id', 'hotel_rate_id', 'price'];

	public function room()
	{
		return $this->belongsTo(HotelRoom::class, 'hotel_room_id')->select('id', 'name');
	}

	public function rate()
	{
		return $this->belongsTo(HotelRate::class, 'hotel_rate_id')->select('id', 'description');
	}
}
