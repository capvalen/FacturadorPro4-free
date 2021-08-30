<?php

namespace Modules\Ecommerce\Http\ViewComposers;

use App\Models\Tenant\Catalogs\Tag;
//use App\Http\Resources\Tenant\ItemEcommerceCollection;


class MenuViewComposer
{
    public function compose($view)
    {
        $view->items = Tag::all();
    }
}
