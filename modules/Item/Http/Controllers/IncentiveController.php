<?php
namespace Modules\Item\Http\Controllers;

use App\Models\Tenant\Item;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\ItemCollection;
use App\Http\Resources\Tenant\ItemResource;
use Exception;
use Illuminate\Http\Request;
use Modules\Item\Http\Resources\IncentiveCollection;


class IncentiveController extends Controller
{
    public function index()
    {
        return view('item::incentives.index');
    }
 
    public function columns()
    {
        return [
            'description' => 'Nombre',
            'internal_id' => 'Código interno',
            'brand' => 'Marca',
            'category' => 'Categoría', 
        ];
    }

    public function records(Request $request)
    {

        $records = $this->getRecords($request);

        return new IncentiveCollection($records->paginate(config('tenant.items_per_page')));
    }
  
    public function getRecords($request){

        switch ($request->column) {

            case 'brand':
                $records = Item::whereHas('brand',function($q) use($request){
                                    $q->where('name', 'like', "%{$request->value}%");
                                })
                                ->whereTypeUser()
                                ->whereNotIsSet()
                                ->orderBy('description');
                break;

            case 'category':
                $records = Item::whereHas('category',function($q) use($request){
                                    $q->where('name', 'like', "%{$request->value}%");
                                })
                                ->whereTypeUser()
                                ->whereNotIsSet()
                                ->orderBy('description');
                break;

            default:
                        
                $records = Item::whereTypeUser()
                                ->whereNotIsSet()
                                ->where($request->column, 'like', "%{$request->value}%")
                                ->orderBy('description');
                break;
        }

        return $records;

    }

    public function record($id)
    {
        $record = new ItemResource(Item::findOrFail($id));

        return $record;
    }

    public function store(Request $request) {

        $request->validate([
            'commission_amount' => 'required|numeric|min:0.01',
        ]);

        $id = $request->input('id');
        $item = Item::findOrFail($id);
        $item->commission_amount = $request->commission_amount;
        $item->commission_type = $request->commission_type;
        $item->update();

        return [
            'success' => true,
            'message' => 'Incentivo registrado con éxito',
            'id' => $item->id
        ];
    }

    public function destroy($id)
    {

        $item = Item::findOrFail($id);
        $item->commission_amount = null;
        $item->commission_type = null;
        $item->update();

        return [
            'success' => true,
            'message' => 'Incentivo eliminado con éxito'
        ];

    }
 


}
