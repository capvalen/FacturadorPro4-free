<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\User;
use App\Http\Resources\Tenant\UserCollection;
use App\Models\Tenant\BankAccount;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Tenant\Company;
use Modules\Finance\Traits\FinanceTrait;
use Modules\Finance\Http\Resources\GlobalPaymentCollection;
use Modules\Finance\Exports\ToPayAllExport;
use Modules\Finance\Exports\ToPayExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;
use App\Models\Tenant\Person;
use Modules\Dashboard\Helpers\DashboardView;
use Modules\Finance\Helpers\ToPay;
use Modules\Finance\Exports\ToPaymentMethodDayExport;


class ToPayController extends Controller
{

    use FinanceTrait;

    public function index(){

        return view('finance::to_pay.index');
    }


    public function filter(){

        $supplier_temp = Person::whereType('suppliers')->orderBy('name')->take(100)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });
        $supplier= [];
        $supplier[]=[
            'id' => null,
            'description' => 'Todos',
            'name' => 'Todos',
            'number' => '',
            'identity_document_type_id' => '',
        ];
        $suppliers = array_merge($supplier,$supplier_temp->toArray());

        $query_users = User::all();
        $users = new UserCollection($query_users);

        $establishments = DashboardView::getEstablishments();

        return compact('suppliers', 'establishments', 'users');
    }


    public function records(Request $request)
    {
        return [
            'records' => (new ToPay())->getToPay($request->all())
       ];
    }

    public function toPayAll()
    {

        return Excel::download(new ToPayAllExport, 'TCuentasPorPagar.xlsx');

    }


    public function toPay(Request $request) {

        $company = Company::first();
        return (new ToPayExport)
                ->company($company)
                ->records((new ToPay())->getToPay($request->all()))
                ->download('Reporte_Cuentas_Por_Pagar'.Carbon::now().'.xlsx');

    }


    public function reportPaymentMethodDays(Request $request)
    {
        // 'records' => (new ToPay())->getToPay($request->all())

        $all_records = (new ToPay())->getToPay($request->all());

        $records = collect($all_records)->where('total_to_pay', '>', 0)->where('type', 'purchase')->map(function($row){
            $row['difference_days'] = Carbon::parse($row['date_of_issue'])->diffInDays($row['date_of_due']);
            return $row;
        });

        $company = Company::first();

        return (new ToPaymentMethodDayExport)
                ->company($company)
                ->records($records)
                ->download('Reporte_C_Pagar_F_Pago'.Carbon::now().'.xlsx');

    }


    public function pdf(Request $request) {

        $records = (new ToPay())->getToPay($request->all());

        $company = Company::first();

        $pdf = PDF::loadView('finance::to_pay.report_pdf', compact("records", "company"));

        $filename = 'Reporte_Cuentas_Por_Pagar_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');

    }


}
