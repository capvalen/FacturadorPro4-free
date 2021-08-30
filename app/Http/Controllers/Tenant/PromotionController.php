<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Tenant\PromotionRequest;
use App\Http\Resources\Tenant\PromotionCollection;
use App\Http\Resources\Tenant\PromotionResource;
use Exception;
use Illuminate\Http\Request;
use App\Models\Tenant\Promotion;
use App\Models\Tenant\Item;
use Modules\Finance\Helpers\UploadFileHelper;


class PromotionController extends Controller
{
    public function index()
    {
        return view('tenant.promotion.index');
    }


    public function columns()
    {
        return [
            'description' => 'Nombre'
            // 'description' => 'Descripción'
        ];
    }

    public function tables()
    {
       
        $items = Item::where('apply_store', 1)->get();
        return compact('items');
    }


    public function records(Request $request)
    {
        $records = Promotion::orderBy('description');
        
        return new PromotionCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create()
    {
        return view('tenant.promotion.form');
    }


    public function record($id)
    {
        $record = new PromotionResource(Promotion::findOrFail($id));
        return $record;
    }

    public function store(PromotionRequest $request) {


        $id = $request->input('id');

        if(!$id)
        {
            $count = Promotion::count();
            if($count > 2)
            {
                return [
                    'success' => false,
                    'message' => 'Solo esta permitido 3 Promociones',
                ];
            }
        }

        $item = Promotion::firstOrNew(['id' => $id]);
        $item->fill($request->all());

        $temp_path = $request->input('temp_path');
        if($temp_path) {

            $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'promotions'.DIRECTORY_SEPARATOR;
            $file_name_old = $request->input('image');
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($temp_path);
            $datenow = date('YmdHis');
            $file_name = Str::slug($item->description).'-'.$datenow.'.'.$file_name_old_array[1];
            Storage::put($directory.$file_name, $file_content);
            $item->image = $file_name;

        }else if(!$request->input('image') && !$request->input('temp_path') && !$request->input('image_url')){
            $item->image = 'imagen-no-disponible.jpg';
        }

        $item->save();

        return [
            'success' => true,
            'message' => ($id)?'Promocion editada con éxito':'Promocion registrada con éxito',
            'id' => $item->id
        ];
    }
    
    public function destroy($id)
    {
        //return 'sd';
        $item = Promotion::findOrFail($id);
        $item->status = 0;
        $item->save();

        return [
            'success' => true,
            'message' => 'Promocion eliminada con éxito'
        ];
    }


    public function upload(Request $request)
    {
        
        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,gif,svg');
        
        if(!$validate_upload['success']){
            return $validate_upload;
        }

        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return $this->upload_image($new_request);
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    function upload_image($request)
    {
        $file = $request['file'];
        $type = $request['type'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }


  


 



}