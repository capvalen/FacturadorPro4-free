<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\BankAccount;
use App\Models\Tenant\Company;
use Modules\Finance\Traits\FinanceTrait; 
use Modules\Finance\Http\Resources\GlobalPaymentCollection;
use Modules\Finance\Exports\PaymentMethodTypeExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;
use Modules\Expense\Models\ExpenseMethodType;
use App\Models\Tenant\Configuration;


class PaymentMethodTypeController extends Controller
{ 

    use FinanceTrait;

    public function index(){
      $configuration = Configuration::first();
      return view('finance::payment_method_types.index', compact('configuration'));
    }


    public function filter(){

        $payment_types = [];
        $destination_types = [];

        return compact('payment_types', 'destination_types');
    }


    public function records(Request $request)
    {

        // dd($request->all());
        $records = $this->getRecords($request->all());
        
        return $records;

    }

    public function getRecords($request){

        $data_of_period = $this->getDatesOfPeriod($request); 

        $params = (object)[
            'date_start' => $data_of_period['d_start'],
            'date_end' => $data_of_period['d_end'],
        ];
        
        $payment_method_types = PaymentMethodType::whereFilterPayments($params)->get();
        $expense_method_types = ExpenseMethodType::whereFilterPayments($params)->get();

        $records_by_pmt = $this->getRecordsByPaymentMethodTypes($payment_method_types);
        $records_by_emt = $this->getRecordsByExpenseMethodTypes($expense_method_types);
        $totals = $this->getTotalsPaymentMethodType($records_by_pmt, $records_by_emt);

        return [
            'records' => $records_by_pmt->merge($records_by_emt),
            'totals' => $totals
        ];
        
    }

    
    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all());

        
        $pdf = PDF::loadView('finance::payment_method_types.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Metodos_de_pago_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }


    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all());

        return (new PaymentMethodTypeExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Metodos_de_pago_'.Carbon::now().'.xlsx');

    }

}
