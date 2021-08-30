<?php

namespace Modules\Ecommerce\Http\ViewComposers;

use App\Models\Tenant\ConfigurationEcommerce;



class InformationContactViewComposer
{
    public function compose($view)
    {
        $view->information = ConfigurationEcommerce::first();
    }
}
