<?php

namespace Modules\Item\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Item\Models\WebPlatform;
use Modules\Item\Http\Requests\WebPlatformRequest;

class WebPlatformController extends Controller
{

    public function index()
    {
        return view('item::web-platforms.index');
    }

    public function records()
    {
        return WebPlatform::get();
    }
 

    public function record($id)
    {
        $record = WebPlatform::findOrFail($id);

        return $record;
    }

    
    public function store(WebPlatformRequest $request)
    {
        $id = $request->input('id');
        $record = WebPlatform::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)? 'Plataforma editada con éxito':'Plataforma registrada con éxito'
        ];
    }


    public function destroy($id)
    {
            
        $record = WebPlatform::findOrFail($id);
        $record->delete(); 

        return [
            'success' => true,
            'message' => 'Plataforma eliminada con éxito'
        ];

    }


}
