<?php

namespace App\Exports\CityMart;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class VendedorXproductoExport implements  FromView, ShouldAutoSize
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

    public function venta_total($venta_total_vendedor) {
        $this->venta_total_vendedor = $venta_total_vendedor;
        return $this;
    }

    public function fecha_consulta($fecha_inicial, $fecha_final) {
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
        return $this;
    }

    public function view(): View {
        return view('order::order_notes.prueba.vendedorXproducto', [
            'records'=> $this->records,
            'fecha_inicial' => $this->fecha_inicial,
            'fecha_final' => $this->fecha_final,
            'vendedor' => $this->vendedor,
            'venta_total_vendedor' => $this->venta_total_vendedor
        ]);
    }
}