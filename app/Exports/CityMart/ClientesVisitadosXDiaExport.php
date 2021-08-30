<?php

namespace App\Exports\CityMart;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClientesVisitadosXDiaExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;
        return $this;
    }

    public function vendedor($vendedor) {
        $this->vendedor = $vendedor;
        return $this;
    }

    public function fecha($fecha) {
        $this->fecha = $fecha;
        return $this;
    }

    public function view(): View {
        return view('order::order_notes.prueba.clientesVisitados', [
            'records'=> $this->records,
            'vendedor' => $this->vendedor,
            'fecha' => $this->fecha,
        ]);
    }
}