<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Http\Resources\InventoryConfigurationResource;
use Modules\Inventory\Models\InventoryConfiguration;
use Modules\Inventory\Http\Requests\InventoryConfigurationRequest;


class InventoryConfigurationController extends Controller
{

    public function index()
    {
        return view('inventory::config.index');
    }

    
    public function record() {

        $inventory_configuration = InventoryConfiguration::first();
        $record = new InventoryConfigurationResource($inventory_configuration);
        
        return $record;

    }

    public function store(InventoryConfigurationRequest $request) {

        $id = $request->input('id');
        $inventory_configuration = InventoryConfiguration::find($id);
        $inventory_configuration->fill($request->all());
        $inventory_configuration->save();
        
        return [
            'success' => true,
            'message' => 'Configuración actualizada'
        ];

    }
    
}
