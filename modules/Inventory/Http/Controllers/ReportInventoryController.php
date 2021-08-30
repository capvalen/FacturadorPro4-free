<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Item;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Exports\InventoryExport;
use Modules\Inventory\Models\Warehouse;
use Modules\Item\Models\Brand;
use Modules\Item\Models\Category;

use Carbon\Carbon;
class ReportInventoryController extends Controller
{
    public function tables()
    {
        return [
            'warehouses' => Warehouse::query()->select('id', 'description')->get(),
            'categories' => Category::query()->select('id', 'name')->get(),
            'brands' => Brand::query()->select('id', 'name')->get(),
        ];
    }

    public function index()
    {
//        $warehouse_id = $request->input('warehouse_id');
//        $reports = $this->getRecords($warehouse_id)->paginate(config('tenant.items_per_page'));
//
//        $warehouses = Warehouse::query()->select('id', 'description')->get();
//
//        return view('inventory::reports.inventory.index', compact('reports', 'warehouses'));
        return view('inventory::reports.inventory.index');
    }

    public function records(Request $request)
    {
        $warehouse_id = $request->input('warehouse_id');
        $filter = $request->input('filter');

        $records = $this->getRecords($warehouse_id);
        if ($request->has('brand_id') && (int)$request->brand_id != 0) {
            $records->where('items.brand_id', $request->brand_id);
        }
        if ($request->has('category_id') && (int)$request->category_id != 0) {
            $records->where('items.category_id', $request->category_id);
        }
        $records->orderBy('items.name','desc');
        $records = $records->latest()->get();

//        return $records;

        $data = [];
        foreach ($records as $row) {
            $add = true;
            if ($filter === '02') {
                $add = ($row->stock < 0);
            }
            if ($filter === '03') {
                $add = ($row->stock == 0);
            }
            if ($filter === '04') {
                $add = ($row->stock > 0 && $row->stock <= $row->item->stock_min);
            }
            if ($filter === '05') {
                $add = ($row->stock > $row->item->stock_min);
            }
            if ($add) {
                $data[] = [
                    'barcode' => $row->item->barcode,
                    'internal_id' => $row->item->internal_id,
                    'name' => $row->item->description,
                    'item_category_name' => optional($row->item->category)->name,
                    'stock_min' => $row->item->stock_min,
                    'stock' => $row->stock,
                    'sale_unit_price' => $row->item->sale_unit_price,
                    'purchase_unit_price' => $row->item->purchase_unit_price,
                    'brand_name' => $row->item->brand->name,
                    'date_of_due' => optional($row->item->date_of_due)->format('d/m/Y'),
                    'warehouse_name' => $row->warehouse->description
                ];
            }
        }

        return $data;
//        return $this->getRecords($warehouse_id, $filter)->get()->transform(function($row) {
//            return [
//                'name' => $row->item->description,
//                'item_category_name' => optional($row->item->category)->name,
//                'stock' => $row->stock,
//                'sale_unit_price' => $row->item->sale_unit_price,
//                'purchase_unit_price' => $row->item->purchase_unit_price,
//                'brand_name' => $row->item->brand->name,
//                'date_of_due' => optional($row->item->date_of_due)->format('d/m/Y'),
//                'warehouse_name' => $row->warehouse->description
//            ];
//        });
    }

    private function getRecords($warehouse_id)
    {
        $query =  ItemWarehouse::with(['item', 'item.category', 'item.brand'])
            ->whereHas('item', function ($q) {
                $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']])
                    ->whereNotIsSet();
            })
            ->join('items', 'items.id', 'item_warehouse.item_id')
            ->select(\DB::raw('item_warehouse.*'));
        if($warehouse_id != 0){
            $query->where('item_warehouse.warehouse_id',$warehouse_id);
        }
        return $query;

    }

    public function export(Request $request)
    {
        try {
            $company = Company::query()->first();
            $establishment = Establishment::query()->first();
            ini_set('max_execution_time', 0);

            $records = $request->input('records');
            $format = $request->input('format');

            if ($format === 'pdf') {
                $pdf = PDF::loadView('inventory::reports.inventory.report', compact('records', 'company', 'establishment', 'format'));
                $pdf->setPaper('A4', 'landscape');
                $filename = 'ReporteInv_' . date('YmdHis');
                return $pdf->download($filename . '.pdf');
            }

            return (new InventoryExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->format($format)
                ->download('ReporteInv_' . Carbon::now() . '.xlsx');

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Search
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $reports = ItemWarehouse::with(['item'])->whereHas('item', function ($q) {
            $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);
            $q->whereNotIsSet();
        })->latest()->get();

        return view('inventory::reports.inventory.index', compact('reports'));
    }

    /**
     * PDF
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {

        $company = Company::first();
        $establishment = Establishment::first();
        ini_set('max_execution_time', 0);

        if ($request->warehouse_id && $request->warehouse_id != 'all') {
            $reports = ItemWarehouse::with(['item', 'item.brand'])->where('warehouse_id', $request->warehouse_id)->whereHas('item', function ($q) {
                $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);
                $q->whereNotIsSet();
            })->latest()->get();
        } else {

            $reports = ItemWarehouse::with(['item', 'item.brand'])->whereHas('item', function ($q) {
                $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);
                $q->whereNotIsSet();
            })->latest()->get();
        }


        $pdf = PDF::loadView('inventory::reports.inventory.report_pdf', compact("reports", "company", "establishment"));
        $pdf->setPaper('A4', 'landscape');
        $filename = 'Reporte_Inventario' . date('YmdHis');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Excel
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request)
    {
        $company = Company::first();
        $establishment = Establishment::first();


        if ($request->warehouse_id && $request->warehouse_id != 'all') {
            $records = ItemWarehouse::with(['item', 'item.brand'])->where('warehouse_id', $request->warehouse_id)->whereHas('item', function ($q) {
                $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);
                $q->whereNotIsSet();
            })->latest()->get();

        } else {
            $records = ItemWarehouse::with(['item', 'item.brand'])->whereHas('item', function ($q) {
                $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);
                $q->whereNotIsSet();
            })->latest()->get();

        }


        return (new InventoryExport)
            ->records($records)
            ->company($company)
            ->establishment($establishment)
            ->download('ReporteInv' . Carbon::now() . '.xlsx');
    }
}
