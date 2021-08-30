<?php

namespace App\Services;
use App\Models\Tenant\Catalogs\UnitType;


class UnitTypeService
{

    public function getDescription($value)
    {
        $row = UnitType::where('id', $value)->first();
        return ($row) ? $row->description : 'NIU';
    }
    

}