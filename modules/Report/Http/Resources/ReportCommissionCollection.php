<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCommissionCollection extends ResourceCollection
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
            
            $total_commision = 0;
            $total_commision_document = 0;
            $total_commision_sale_note = 0;

            $total_transactions_document = $row->documents->count();
            $total_transactions_sale_note = $row->sale_notes->count();
            $total_transactions = $total_transactions_document + $total_transactions_sale_note;

            $acum_sales_document = $row->documents->sum('total');
            $acum_sales_sale_note = $row->sale_notes->sum('total');
            $acum_sales = $acum_sales_document + $acum_sales_sale_note;


            foreach ($row->documents as $document) {
                // $total_commision_document += $document->items->sum('relation_item.commission_amount'); 
                foreach ($document->items as $item) {
                    if ($item->relation_item->commission_amount) {
                        
                        if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                            $total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                        }
                        else{

                            $total_commision_document += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                            
                        }

                    }
                } 

            }

            foreach ($row->sale_notes as $sale_note) {
                // $total_commision_sale_note += $sale_note->items->sum('relation_item.commission_amount'); 
                foreach ($sale_note->items as $item) {
                    
                    if ($item->relation_item->commission_amount) {
                    
                        if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                            $total_commision_sale_note += $item->quantity * $item->relation_item->commission_amount;
                        }
                        else{

                            $total_commision_sale_note += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                            
                        }
                    //     $total_commision_sale_note += ($item->quantity * $item->relation_item->commission_amount);
                    }
                    
                }
            }

            $total_commision = $total_commision_document + $total_commision_sale_note;

            return [
                'id' => $row->id,  
                'user_name' => $row->name,
                'acum_sales' => number_format($acum_sales,2),
                'acum_sales_document' => $acum_sales_document,
                'acum_sales_sale_note' => $acum_sales_sale_note,
                'total_commision' => number_format($total_commision, 2),
                'total_commision_sale_note' => $total_commision_sale_note,
                'total_commision_document' => $total_commision_document,
                'total_transactions' => $total_transactions,
                'total_transactions_document' => $total_transactions_document,
                'total_transactions_sale_note' => $total_transactions_sale_note,
            ];
        });
    }
    
}
