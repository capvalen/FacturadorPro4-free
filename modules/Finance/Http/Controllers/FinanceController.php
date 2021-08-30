<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\BankAccount;

class FinanceController extends Controller
{ 

    public function records(Request $request)
    {

        $records = GlobalPayment::whereDestinationType(Cash::class)->first();

        
        return $records->destination->cash_documents;

    }

}
