<?php

namespace Modules\Inventory\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class KardexExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function balance($balance) {
        $this->balance = $balance;

        return $this;
    }

    public function item_id($item_id) {
        $this->item_id = $item_id;

        return $this;
    }

    public function models($models) {
        $this->models = $models;

        return $this;
    }

    public function records($records) {
        $this->records = $records;

        return $this;
    }

    public function company($company) {
        $this->company = $company;

        return $this;
    }

    public function establishment($establishment) {
        $this->establishment = $establishment;

        return $this;
    }

    public function item($item) {
        $this->item = $item;

        return $this;
    }

    public function view(): View {
        $userWarehouse = auth()->user()->establishment_id;
        return view('inventory::reports.kardex.report_excel', [
            'item_id'=> $this->item_id,
            'balance'=> $this->balance,
            'records'=> $this->records,
            'models'=> $this->models,
            'company' => $this->company,
            'establishment'=>$this->establishment,
            'item'=> $this->item,
            'userWarehouse' => $userWarehouse
        ]);
    }
}
