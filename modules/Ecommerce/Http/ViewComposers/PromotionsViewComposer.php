<?php

namespace Modules\Ecommerce\Http\ViewComposers;

use App\Models\Tenant\Promotion;


class PromotionsViewComposer
{
    public function compose($view)
    {
        $view->items = Promotion::all();
    }
}