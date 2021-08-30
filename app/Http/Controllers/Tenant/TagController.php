<?php
namespace App\Http\Controllers\Tenant;

use App\Imports\ItemsImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\Tenant\TagRequest;
use App\Http\Resources\Tenant\TagCollection;
use App\Http\Resources\Tenant\TagResource;
use Exception;
use Illuminate\Http\Request;
use App\Models\Tenant\ItemTag;
use App\Models\Tenant\Catalogs\Tag;



class TagController extends Controller
{
    public function index()
    {
        return view('tenant.tags.index');
    }


    public function columns()
    {
        return [
            'description' => 'Nombre'
            // 'description' => 'Descripción'
        ];
    }

    public function records(Request $request)
    {
        $records = Tag::where($request->column, 'like', "%{$request->value}%")->orderBy('description');
        
        return new TagCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create()
    {
        return view('tenant.items.form');
    }


    public function record($id)
    {
        $record = new TagResource(Tag::findOrFail($id));

        return $record;
    }

    public function store(TagRequest $request) {
        $id = $request->input('id');
        $person = Tag::firstOrNew(['id' => $id]);
        $person->fill($request->all());
        $person->save();

        return [
            'success' => true,
            'message' => ($id)?'Tag editado con éxito':'Tag registrado con éxito',
            'id' => $person->id
        ];
    }
    
    public function destroy($id)
    {
        //return 'sd';
        $item = Tag::findOrFail($id);
       // $this->deleteRecordInitialKardex($item);
        $item->status = 0;
        $item->save();

       // $item->delete();

        return [
            'success' => true,
            'message' => 'Tag eliminado con éxito'
        ];
    }



  


 



}