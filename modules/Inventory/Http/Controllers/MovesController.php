<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Http\Resources\InventoryCollection;
use Modules\Inventory\Http\Resources\InventoryCollection2;

use Modules\Inventory\Http\Resources\InventoryResource;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Requests\InventoryRequest;
use Modules\Item\Models\ItemLot;

class MovesController extends Controller
{
    use InventoryTrait;

    public function index()
    {
        return view('inventory::moves.index');
    }

    public function columns()
    {
        return [
            'description' => 'Producto',
        ];
    }

    public function records(Request $request)
    {
        $records = Inventory::with('item', 'warehouse', 'warehouse_destination')->where('description', 'Traslado');

        if($request->column == 'description')
        {
            $records = $records
            ->whereHas('item', function($query) use($request) {
                $query->where('name', 'like', '%' . $request->value . '%');

            });

        }

        return new InventoryCollection2($records->paginate(config('tenant.items_per_page')));
    }


    public function record($id)
    {
        $record = new InventoryResource(ItemWarehouse::with(['item', 'warehouse'])->findOrFail($id));

        return $record;
    }












}
