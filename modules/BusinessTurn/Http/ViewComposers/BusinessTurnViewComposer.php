<?php

namespace Modules\BusinessTurn\Http\ViewComposers;

use Modules\BusinessTurn\Models\BusinessTurn;

class BusinessTurnViewComposer
{
    public function compose($view)
    { 
        $view->vc_business_turns = BusinessTurn::where('active',true)->pluck('value')->toArray();
    }
}