<?php

namespace Modules\Report\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class GeneralItemExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;

        return $this;
    }

    public function type($type) {
        $this->type = $type;

        return $this;
    }

    public function document_type_id($type) {
        $this->document_type_id = $type;
        return $this;
    }

    public function view(): View {
        return view('report::general_items.report_excel', [
            'records'=> $this->records,
            'type'=> $this->type,
            'document_type_id' => $this->document_type_id
        ]);
    }
}
