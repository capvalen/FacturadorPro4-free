<?php

namespace Modules\Item\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Item\Models\ItemLot;
use Modules\Item\Http\Resources\ItemLotCollection;
use Modules\Item\Http\Resources\ItemLotResource;
use Modules\Item\Http\Requests\ItemLotRequest;
use Modules\Item\Exports\ItemLotExport;
use Carbon\Carbon;


class ItemLotController extends Controller
{

    public function index()
    {
        return view('item::item-lots.index');
    }


    public function columns()
    {
        return [
            'series' => 'Serie',
            'date' => 'Fecha',
            'state' => 'Estado',
            'item_description' => 'Producto',
        ];
    }


    public function records(Request $request)
    {
        
        $records = $this->getRecords($request);

        return new ItemLotCollection($records->paginate(config('tenant.items_per_page')));
    
    }


    public function getRecords($request){

        if($request->column == 'item_description'){
            
            $records = ItemLot::whereHas('item', function($query) use($request){
                            $query->where('description', 'like', "%{$request->value}%")->latest();
                        });

        }else{
            $records = ItemLot::where($request->column, 'like', "%{$request->value}%")->latest();
        }

        return $records;
    }


    public function record($id)
    {
        $record = ItemLot::findOrFail($id);

        return $record;
    }


    public function store(ItemLotRequest $request)
    {

        $id = $request->input('id');
        $record = ItemLot::findOrFail($id);
        $record->series = $request->series;
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Serie editada con éxito':'Serie registrada con éxito',
        ];

    }

    public function export(Request $request)
    {

        $records = $this->getRecords($request)->get();

        return (new ItemLotExport)
                ->records($records)
                ->download('Series_'.Carbon::now().'.xlsx');

    }

}
