<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRoom extends ModelTenant
{
	protected $table = 'hotel_rooms';

	public static $status = ['DISPONIBLE', 'MANTENIMIENTO', 'OCUPADO', 'LIMPIEZA'];

	protected $fillable = ['name', 'hotel_category_id', 'hotel_floor_id', 'active', 'description', 'item_id'];

	public function category()
	{
		return $this->belongsTo(HotelCategory::class, 'hotel_category_id')->select('id', 'description');
	}

	public function rates()
	{
		return $this->hasMany(HotelRoomRate::class);
	}

	public function floor()
	{
		return $this->belongsTo(HotelFloor::class, 'hotel_floor_id')->select('id', 'description');
	}

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
