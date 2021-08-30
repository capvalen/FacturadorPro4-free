<?php

namespace Modules\Sale\Services;

use Illuminate\Support\Facades\Storage;

class SaleOpportunityFileService
{

    public function getFile($filename)
    {
        $file =  Storage::disk('tenant')->get('sale_opportunity_files'.DIRECTORY_SEPARATOR.$filename);
        $temp = tempnam(sys_get_temp_dir(), 'tmp_sale_opportunity_files');
        file_put_contents($temp, $file);
        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        $image = 'data:' . $mime . ';base64,' . base64_encode($data);
        
        return $image;
    }
    

    public function isImage($filename)
    {

        $image_types = [
            'jpeg',
            'jpg',
            'png',
            'svg',
            'bmp',
            'tiff',
        ];

        $array_filename = explode('.', $filename);
         
        return (in_array($array_filename[1], $image_types)) ?? false;
    }

}