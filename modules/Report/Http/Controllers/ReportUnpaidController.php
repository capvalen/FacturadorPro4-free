<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\NoPaidExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\QuotationCollection;
use Modules\Dashboard\Helpers\DashboardView; 
use Modules\Finance\Traits\UnpaidTrait; 

class ReportUnpaidController extends Controller
{

    use UnpaidTrait;
    
    public function excel(Request $request) {

        $records = $this->transformRecords((new DashboardView())->getUnpaidFilterUser($request->all())->get());

        $company = Company::first();
        return (new NoPaidExport)
                ->company($company)
                ->records($records)
                ->download('Reporte_Cuentas_Por_Cobrar'.Carbon::now().'.xlsx');

    }


}
