<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class PruebaExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;
        return $this;
    }

    public function fecha_consulta($fecha_consulta) {
        $this->fecha_consulta = $fecha_consulta;
        return $this;
    }

    public function view(): View {
        return view('order::order_notes.prueba.tabla', [
            'records'=> $this->records,
            'fecha_consulta' => $this->fecha_consulta,
        ]);
    }
}