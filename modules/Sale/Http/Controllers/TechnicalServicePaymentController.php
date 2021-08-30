<?php
namespace Modules\Sale\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cash;
use App\Models\Tenant\CashDocument;
use Modules\Sale\Http\Requests\TechnicalServicePaymentRequest;
use Modules\Sale\Http\Resources\TechnicalServicePaymentCollection;
use App\Models\Tenant\PaymentMethodType;
use Modules\Sale\Models\TechnicalService;
use Modules\Finance\Traits\FinanceTrait;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Models\TechnicalServicePayment;

class TechnicalServicePaymentController extends Controller
{

    use FinanceTrait;

    public function records($technical_service_id)
    {
        $records = TechnicalServicePayment::where('technical_service_id', $technical_service_id)->get();

        return new TechnicalServicePaymentCollection($records);
    }

    public function tables()
    {
        return [
            'payment_method_types' => PaymentMethodType::all(),
            'payment_destinations' => $this->getPaymentDestinations()
        ];
    }


    public function document($technical_service_id)
    {
        $record = TechnicalService::find($technical_service_id);

        $total_paid = round(collect($record->payments)->sum('payment'), 2);
        $total = $record->cost;
        $total_difference = round($total - $total_paid, 2);

        return [
            'number_full' => $record->number_full,
            'total_paid' => $total_paid,
            'total' => $total,
            'total_difference' => $total_difference,
        ];
    }


    public function store(TechnicalServicePaymentRequest $request)
    {
        $id = $request->input('id');

        DB::connection('tenant')->transaction(function () use ($id, $request) {

            $record = TechnicalServicePayment::query()->firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();
            $this->createGlobalPayment($record, $request->all());
        });

        return [
            'success' => true,
            'message' => ($id)?'Pago editado con éxito':'Pago registrado con éxito'
        ];
    }


    public function destroy($id)
    {
        $item = TechnicalServicePayment::findOrFail($id);
        $item->delete();

        return [
            'success' => true,
            'message' => 'Pago eliminado con éxito'
        ];
    }



}
