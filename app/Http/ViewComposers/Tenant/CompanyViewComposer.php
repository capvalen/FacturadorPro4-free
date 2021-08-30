<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\Tenant\Company;
use App\Models\Tenant\Order;

class CompanyViewComposer
{
    public function compose($view)
    {
        $view->vc_company = Company::first();
        $view->vc_orders = Order::where('status_order_id', 1)
            ->count();
    }
}
