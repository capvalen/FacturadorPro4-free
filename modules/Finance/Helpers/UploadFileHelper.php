<?php

namespace Modules\Finance\Helpers; 

use Validator;

class UploadFileHelper
{ 

    public static function validateUploadFile($request, $column = 'file', $mimes = 'jpg,jpeg,png,gif,svg,pdf,xlsx')
    {
        
        $validator = Validator::make($request->all(), [
            $column => 'mimes:'.$mimes
        ]);

        if ($validator->fails()) { 
            return [
                'success' => false,
                'message' =>  'Tipo de archivo no permitido',
            ];
        }

        return [
            'success' => true,
            'message' =>  '',
        ];

    } 

 
}
