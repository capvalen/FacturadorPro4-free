<?php

namespace Modules\Finance\Http\Controllers;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\BankAccount;
use App\Models\Tenant\Company;
use Modules\Finance\Traits\FinanceTrait; 
use Modules\Finance\Http\Resources\GlobalPaymentCollection;
use Modules\Finance\Exports\BalanceExport;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;

class BalanceController extends Controller
{ 

    use FinanceTrait;

    public function index(){
        $configuration = Configuration::first();
        return view('finance::balance.index',compact('configuration'));
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
        
        $bank_accounts = BankAccount::with(['global_destination' => function($query) use($params){
                                        $query->whereFilterPaymentType($params);
                                    }])
                                    ->get();
                                    
        $all_cash = GlobalPayment::whereFilterPaymentType($params)
                                    ->with(['payment'])
                                    ->whereDestinationType(Cash::class)
                                    ->get();


        $balance_by_bank_acounts = $this->getBalanceByBankAcounts($bank_accounts);
        $balance_by_cash = $this->getBalanceByCash($all_cash);

        return $balance_by_bank_acounts->push($balance_by_cash);
        
    }

    
    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all());

        $pdf = PDF::loadView('finance::balance.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Balance_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }


    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all());

        return (new BalanceExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Balance_'.Carbon::now().'.xlsx');

    }

}
