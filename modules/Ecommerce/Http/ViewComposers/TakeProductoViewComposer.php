<?php

namespace App\Http\View\Composers;

use App\Repositories\UserRepository;
use Illuminate\View\View;
use App\Models\Tenant\Item;

class TakeProductoViewComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $records = Item::orderBy('id', 'DESC')->take(2)->get();

         $view->with('records', $records);
    }
}