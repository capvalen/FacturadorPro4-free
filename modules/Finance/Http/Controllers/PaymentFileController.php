<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\PaymentFile;
use App\Models\Tenant\Cash;
use Illuminate\Support\Facades\Storage;
use Modules\Finance\Helpers\UploadFileHelper;

class PaymentFileController extends Controller
{ 

    public function download($filename, $type) { 
        return Storage::disk('tenant')->download('payment_files'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$filename);
    }

    public function uploadAttached(Request $request)
    {
        // dd($request->all());
        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file');
        
        if(!$validate_upload['success']){
            return $validate_upload;
        }

        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
                'index' => $request->input('index'),
            ];

            return $this->upload_attached($new_request);
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }


    function upload_attached($request)
    {
        $file = $request['file'];
        $type = $request['type'];
        $index = $request['index'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'index' => (int) $index,
                // 'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }


}
