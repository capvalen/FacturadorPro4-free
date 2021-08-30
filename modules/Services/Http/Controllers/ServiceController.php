<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Services\Data\ServiceData;

class ServiceController extends Controller
{
    public function service($type, $number)
    {
        return ServiceData::service($type, $number);
    }
}
