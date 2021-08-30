<?php

namespace Modules\Offline\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Offline\Models\OfflineConfiguration; 
use Modules\Offline\Http\Resources\OfflineConfigurationResource;
use Modules\Offline\Http\Requests\OfflineConfigurationRequest; 

class OfflineConfigurationController extends Controller
{
     
    public function index()
    {
        return view('offline::offline_configurations.index');
    }
 
 
    public function record() {
        $offline_configuration = OfflineConfiguration::first();
        $record = new OfflineConfigurationResource($offline_configuration);
        
        return $record;
    }
    
    public function store(OfflineConfigurationRequest $request) {
        $id = $request->input('id');
        $offline_configuration = OfflineConfiguration::find($id);
        $offline_configuration->fill($request->all());
        $offline_configuration->save();
        
        return [
            'success' => true,
            'message' => 'Configuración offline actualizada'
        ];
    }
    
}
