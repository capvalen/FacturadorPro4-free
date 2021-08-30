<?php

namespace Modules\Finance\Traits;

use Carbon\Carbon;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNotePayment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FilePaymentTrait
{

    public function saveFiles($record, $request, $type)
    {

        $temp_path = $request->temp_path;

        if($temp_path) {

            $file_name_old = $request->filename;
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($temp_path);
            $extension = $file_name_old_array[1];
            $file_name = Str::slug($file_name_old_array[0])."-{$type}-".$record->id.'.'.$extension;

            $record->payment_file()->create([
                'filename' => $file_name
            ]);

            Storage::disk('tenant')->put('payment_files'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file_name, $file_content);


        }

    }


}
