<?php

namespace App\Http\ViewComposers\Tenant;

class UserViewComposer
{
    public function compose($view)
    {
        $view->vc_user = auth()->user();
    }
}