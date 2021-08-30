<?php
namespace Modules\Sale\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sale\Http\Requests\QuotationPaymentRequest;
use Modules\Sale\Http\Resources\QuotationPaymentCollection;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Quotation;
use Modules\Finance\Traits\FinanceTrait;
use Modules\Finance\Traits\FilePaymentTrait;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Models\QuotationPayment;

class QuotationPaymentController extends Controller
{

    use FinanceTrait, FilePaymentTrait;

    public function records($quotation_id)
    {
        $records = QuotationPayment::where('quotation_id', $quotation_id)->get();

        return new QuotationPaymentCollection($records);
    }

    public function tables()
    {
        return [
            'payment_method_types' => PaymentMethodType::all(),
            'payment_destinations' => $this->getPaymentDestinations()
        ];
    }

    public function document($quotation_id)
    {
        $record = Quotation::find($quotation_id);

        $total_paid = round(collect($record->payments)->sum('payment'), 2);
        $total = $record->total;
        $total_difference = round($total - $total_paid, 2);

        return [
            'number_full' => $record->identifier,
            'total_paid' => $total_paid,
            'total' => $total,
            'total_difference' => $total_difference,
        ];
    }

    public function store(QuotationPaymentRequest $request)
    {
        $id = $request->input('id');

        DB::connection('tenant')->transaction(function () use ($id, $request) {

            $record = QuotationPayment::firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();
            $this->createGlobalPayment($record, $request->all());
            $this->saveFiles($record, $request, 'quotations');

        });
 
        return [
            'success' => true,
            'message' => ($id)?'Pago editado con éxito':'Pago registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $item = QuotationPayment::findOrFail($id);
        $item->delete();

        return [
            'success' => true,
            'message' => 'Pago eliminado con éxito'
        ];
    }
 


}
