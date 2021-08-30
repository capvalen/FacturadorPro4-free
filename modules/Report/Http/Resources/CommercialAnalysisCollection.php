<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
use App\Models\Tenant\Item;
use Modules\Item\Models\Category;
use Modules\Report\Traits\ReportAnalysisTrait;


class CommercialAnalysisCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key){ 
             
            $customer = $row;
            $documents = $row->documents;

            $country =($customer->country_id)? $customer->country->description : '' ;
            $district = ($customer->district_id)? '-'.$customer->district->description : '' ;
            $province = ($customer->province_id)? '-'.$customer->province->description : '' ;
            $department = ($customer->department_id)? '-'.$customer->department->description : '' ;
 
            $first_document_date = ($documents) ? ($documents->first() ? $documents->first()->date_of_issue->format('d-m-Y'):null):null;
            $last_document_date = ($documents) ? ($documents->last() ? $documents->last()->date_of_issue->format('d-m-Y'):null):null;
            $quantity_visit = $documents->count();
            $total = $documents->sum('total');
            
            $acum_difference_days = 0;
            $acum_comparations = 0;

            if($first_document_date && $last_document_date){
                for ($i=0; $i < $quantity_visit; $i++) { 

                    $doc = $documents[$i];
                    // dd($doc->date_of_issue);
                    if(($i+1) < $quantity_visit){
                        
                        $f_date = $doc->date_of_issue;
                        $acum_difference_days += $f_date->diffInDays($documents[$i+1]->date_of_issue);
                        $acum_comparations++;
                    }
                }
            }

            $prom_difference_days = ($acum_comparations > 0) ? number_format($acum_difference_days / $acum_comparations,2) : 0;

            $contact_date = (Carbon::parse($last_document_date))->addDays($prom_difference_days);
            
            // dd($difference_days);


            $calculate_categories_count = array();
            $categories = Category::all()->pluck('name');
            foreach ($categories as $item) {
                $calculate_categories_count[strtoupper($item)] = 0;
            }

            if($quantity_visit > 0){
                foreach ($documents as $doc) {
                    foreach ($doc->items as $it) {

                        $item = Item::findOrFail($it->item_id);
                        if($item->category){
                            $name_category = strtoupper($item->category->name);
                            $calculate_categories_count[$name_category] = $calculate_categories_count[$name_category] + $it->quantity;
                        }
                    }
                }
            }

            $result = [
                'id' => $row->id, 
                'number' => $row->number_full,
                'customer_name' => $row->name,
                'customer_doc' => $row->identity_document_type->description,
                'customer_number' => $row->number,
                'zone' => "{$country} {$department} {$province} {$district}",
                'telephone' => $row->telephone,
                'first_document' => ($documents) ? ($documents->first() ? $documents->first()->series.'-'.$documents->first()->number:'-'):'-',
                'last_document' => ($documents) ? ($documents->last() ? $documents->last()->series.'-'.$documents->last()->number:'-'):'-',
                'first_document_date' => $first_document_date,
                'last_document_date' => $last_document_date,
                'prom_difference_days' => $prom_difference_days,
                'quantity_visit' => $quantity_visit,
                'total' => $total,
                'contact_date' => $contact_date->format('d-m-Y'),
            ];

            return array_merge($result, $calculate_categories_count);
        });
    }
    
}
