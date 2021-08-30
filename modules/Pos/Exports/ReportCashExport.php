<?php

namespace Modules\Pos\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportCashExport implements  FromView
{
    use Exportable;

    protected $data = [];

    public function cash($cash)
    {
        $this->cash = $cash;
        return $this;
    }

    public function company($company)
    {
        $this->company = $company;
        return $this;
    }

    public function methods_payment($methods_payment)
    {
        $this->methods_payment = $methods_payment;
        return $this;
    }

    public function getData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Establece el valor de Data
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData($data = []){
        $this->data = $data;
        return $this;
    }

//    public function view(): View {
//        return view('pos::cash.report_excel', [
//            'methods_payment'=> $this->methods_payment,
//            'cash'=> $this->cash,
//            'company' => $this->company,
//        ]);
//    }

    public function view(): View
    {
        return view('pos::cash.report_excel', ['data'=> $this->data,]);
    }
}
