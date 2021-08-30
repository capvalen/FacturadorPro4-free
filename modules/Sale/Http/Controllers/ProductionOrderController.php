<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Sale\Models\Contract;
use Modules\Sale\Http\Resources\ProductionOrderCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use Exception;


class ProductionOrderController extends Controller
{


    public function index()
    {
        return view('sale::production_orders.index');
    } 

    public function columns()
    {
        return [
            'delivery_date' => 'Fecha de entrega',
            'date_of_issue' => 'Fecha de emisión',
            'user_name' => 'Vendedor',
            'customer' => 'Cliente',
        ];
    }
 
    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new ProductionOrderCollection($records->paginate(config('tenant.items_per_page')));
    }

    private function getRecords($request){

        if($request->column == 'user_name'){
            
            $records = Contract::whereHas('user', function($query) use($request){
                            $query->where('name', 'like', "%{$request->value}%");
                        });

        }
        else if($request->column == 'customer'){
            
            $records = Contract::whereHas('person', function($query) use($request){
                            $query->where('name', 'like', "%{$request->value}%")
                                    ->orWhere('number', 'like', "%{$request->value}%");
                        });

        }
        else{

            $records = Contract::where($request->column, 'like', "%{$request->value}%");
        
        }
        
        return $records->where('delivery_date', '!=', null)->whereTypeUser()->orderBy('delivery_date');
    }
 
}
