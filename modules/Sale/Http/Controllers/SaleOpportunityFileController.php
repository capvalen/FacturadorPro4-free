<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Modules\Sale\Models\SaleOpportunityFile;
use Illuminate\Support\Facades\Storage;
use Modules\Finance\Helpers\UploadFileHelper;

class SaleOpportunityFileController extends Controller
{

    public function saveFiles($sale_opportunity, $files)
    {

        foreach ($files as $row) {
                
            $file = isset($row['response']['data']) ? $row['response']['data'] : null;

            if($file){

                $temp_path = $file['temp_path']; 

                $file_name_old = $file['filename'];
                $file_name_old_array = explode('.', $file_name_old);
                $file_content = file_get_contents($temp_path);
                $extension = $file_name_old_array[1];
                $file_name = Str::slug($file_name_old_array[0]).'-'.$sale_opportunity->id.'.'.$extension;

                Storage::disk('tenant')->put('sale_opportunity_files'.DIRECTORY_SEPARATOR.$file_name, $file_content);
    
            }else{

                $file_name = $row['filename'];

            }

            
            $sale_opportunity->files()->create([
                'filename' => $file_name
            ]);

        }

    } 
 
    public function uploadFile(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file');
        
        if(!$validate_upload['success']){
            return $validate_upload;
        }
        
        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return $this->upload_file($new_request);
        }
        return [
            'success' => false,
            'message' =>  'No es un archivo',
        ];
    }


    function upload_file($request)
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
            ]
        ];

    }

    public function download($filename) { 
        return Storage::disk('tenant')->download('sale_opportunity_files'.DIRECTORY_SEPARATOR.$filename);
    }

}
