<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Report\Helpers\UserCommissionHelper;

class ReportUserCommissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {

            $utilities = UserCommissionHelper::getUtilities($row->sale_notes, $row->documents);
            $commission = UserCommissionHelper::getCommission($row, $utilities);
 
            return [
                'id' => $row->id,  
                'user_name' => $row->name,
                'type' => ($row->user_commission->type == 'amount') ? 'Monto':'Porcentaje',
                'commission' => $commission,
                'amount' => $row->user_commission->amount,
                'total_utility' => $utilities['total_utility'],
            ];
        });
    }
    
}
