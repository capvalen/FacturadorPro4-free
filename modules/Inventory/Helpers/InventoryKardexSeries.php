<?php

namespace Modules\Inventory\Helpers;

use Carbon\Carbon;

class InventoryKardexSeries
{

    public static function transformRecords($records)
    {
        
        return $records->transform(function($row, $key){
          
            $status = '';

            if($row->has_sale){
                $status = 'VENDIDO';
            }else{
                $status = 'DISPONIBLE';
            }

            if($row->has_sale && $row->state == 'Inactivo'){
                $status = 'NO DISPONIBLE';
            }

            return [
                'id' => $row->id,
                'series' => $row->series,
                'name_item' => $row->item->description,
                'und_item' => $row->item->unit_type_id,
                'code_item' => $row->item->internal_id,
                // 'status' => ($row->has_sale == 1 ? 'VENDIDO' : 'DISPONIBLE'),
                'status' => $status,
                'date' => $row->date
            ];
            
        });

    } 

 
}