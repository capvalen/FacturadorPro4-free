<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait BackupTrait
{ 

    public function getErrorMessage($message) {

        return [
            'success' => false,
            'message' => $message
        ];

    }
 

    public function setErrorLog($exception)
    {
        Log::error("Line: {$exception->getLine()} - Message: {$exception->getMessage()} - File: {$exception->getFile()}");
    }


}
