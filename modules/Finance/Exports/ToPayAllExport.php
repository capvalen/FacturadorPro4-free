<?php

namespace Modules\Finance\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Tenant\Establishment; 
use Carbon\Carbon;
use Modules\Finance\Helpers\ToPay;

class ToPayAllExport implements FromView
{

    public function view(): View
    { 

        $records =  ToPay::getToPayNoFilter();
        
        $company = DB::connection('tenant')->table('companies')->select('name', 'number')->get();

        return view('finance::to_pay.reportall_excel',['records' => $records,
            'companies' => $company ]);
         
    }

}
