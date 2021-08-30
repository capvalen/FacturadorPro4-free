<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ChargeDiscountRequest;
use App\Http\Resources\Tenant\ChargeDiscountCollection;
use App\Http\Resources\Tenant\ChargeDiscountResource;
use App\Models\Tenant\Catalogs\Code;
use App\Models\Tenant\ChargeDiscount;

class ChargeDiscountController extends Controller
{
    public function index()
    {
        return view('tenant.charge_discounts.index');
    }

    public function records($type)
    {
        $records = ChargeDiscount::where('type', $type)->get();

        return new ChargeDiscountCollection($records);
    }

    public function create()
    {
        return view('tenant.charge_discounts.form');
    }

    public function tables($type)
    {
        $charge_discount_types = collect(Code::where('catalog_id', '53')
                                        ->where('type', $type)
                                        ->where('active', true)
                                        ->get())->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->description.' - '.ucfirst($row->level),
            ];
        });

        return compact('charge_discount_types');
    }

    public function record($id)
    {
        $record = new ChargeDiscountResource(ChargeDiscount::findOrFail($id));

        return $record;
    }

    public function store(ChargeDiscountRequest $request)
    {
        $id = $request->input('id');
        $discount = ChargeDiscount::firstOrNew(['id' => $id]);
        $discount->fill($request->all());
        $discount->save();

        return [
            'success' => true,
            'message' => ($id)?'Descuento editado con éxito':'Descuento registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $discount = ChargeDiscount::findOrFail($id);
        $discount->delete();

        return [
            'success' => true,
            'message' => 'Descuento eliminado con éxito'
        ];
    }
}