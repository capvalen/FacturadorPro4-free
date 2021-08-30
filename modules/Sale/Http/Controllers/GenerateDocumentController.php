<?php

namespace Modules\Sale\Http\Controllers;

use App\CoreFacturalo\Requests\Inputs\DocumentInput;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Controllers\Tenant\SaleNoteController;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Item;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Person;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\Series;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Traits\FinanceTrait;
use Modules\Sale\Http\Resources\TechnicalServiceResource;
use Modules\Sale\Models\TechnicalService;

class GenerateDocumentController extends Controller
{
    use FinanceTrait;

    public function tables()
    {
        $establishment = Establishment::query()->where('id', auth()->user()->establishment_id)->first();
        $series = Series::query()->where('establishment_id',$establishment->id)->get();
        $document_types = [
            ['id' => '01', 'name' => 'Factura'],
            ['id' => '03', 'name' => 'Boleta'],
            ['id' => 'nv', 'name' => 'Nota de venta'],
        ];
        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();

        return compact('series', 'document_types', 'payment_method_types', 'payment_destinations', 'establishment');
    }

    public function record($table, $id)
    {
        if($table === 'technical-services') {
            return new TechnicalServiceResource(TechnicalService::query()->findOrFail($id));
        }
    }

    public function customers(Request $request)
    {
        $customers = Person::query()->where('number','like', "%{$request->input}%")
            ->orWhere('name','like', "%{$request->input}%")
            ->whereType('customers')
            ->whereIsEnabled()
            ->orderBy('name')
            ->get()->transform(function($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number.' - '.$row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_code' => $row->identity_document_type->code,
                    'addresses' => $row->addresses,
                    'address' =>  $row->address
                ];
            });

        return compact('customers');
    }

    public function store(Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $inputs = $request->all();
            $inputs['items'][0]['item_id'] = $this->storeItem($request->input('items')[0]);
            if (in_array($request->input('document_type_id'), ['01', '03'])) {
                $res = (new DocumentController())->storeWithData(DocumentInput::set($inputs));
            } else {
                $inputs['items'] = DocumentInput::items($inputs);
                $inputs['type_period'] = null;
                $inputs['quantity_period'] = null;
                $res = (new SaleNoteController())->storeWithData($inputs);
            }

            DB::connection('tenant')->commit();
            return $res;

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return [
                'success' => false,
                'message' => $e->getFile().'-'.$e->getLine().'-'.$e->getMessage()
            ];
        }
    }

    public function storeItem($row)
    {
        $item = Item::query()->create([
            'internal_id' => $row['internal_id'],
            'description' => $row['description'],
            'name' => null,
            'second_name' => null,
            'item_type_id' => $row['item_type_id'],
            'unit_type_id' => $row['unit_type_id'],
            'currency_type_id' => $row['currency_type_id'],
            'sale_unit_price' => $row['unit_price'],
            'sale_affectation_igv_type_id' => $row['affectation_igv_type_id'],
            'purchase_affectation_igv_type_id' => $row['affectation_igv_type_id'],
            'stock' => 0,
        ]);

        return $item->id;
    }
//    private function searchCustomers($input)
//    {
//        return  Person::query()->where('number','like', "%{$input}%")
//        ->orWhere('name','like', "%{$input}%")
//        ->whereType('customers')
//        ->whereIsEnabled()
//        ->orderBy('name')
//        ->get()->transform(function($row) {
//            return [
//                'id' => $row->id,
//                'description' => $row->number.' - '.$row->name,
//                'name' => $row->name,
//                'number' => $row->number,
//                'identity_document_type_id' => $row->identity_document_type_id,
//                'identity_document_type_code' => $row->identity_document_type->code,
//                'addresses' => $row->addresses,
//                'address' =>  $row->address
//            ];
//        });
//    }
}
