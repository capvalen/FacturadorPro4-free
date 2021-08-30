<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\System\Configuration;
use App\Models\Tenant\Module;

class ModuleViewComposer
{
    public function compose($view)
    {
        $modules = auth()->user()->modules()->pluck('value')->toArray();
        $systemConfig = Configuration::select('use_login_global')->first();
        if(count($modules) > 0) {
            $view->vc_modules = $modules;
        } else {
            $view->vc_modules = Module::all()->pluck('value')->toArray();
        }
        $view->useLoginGlobal = $systemConfig->use_login_global;
    }
}
