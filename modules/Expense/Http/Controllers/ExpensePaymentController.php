<?php
namespace Modules\Expense\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Expense\Http\Resources\ExpensePaymentCollection;
use Modules\Expense\Http\Requests\ExpensePaymentRequest;
use Modules\Expense\Models\Expense;
use Modules\Expense\Models\ExpenseMethodType;
use Modules\Expense\Models\ExpensePayment;
use Modules\Finance\Traits\FinanceTrait; 
use Modules\Finance\Traits\FilePaymentTrait; 
use Illuminate\Support\Facades\DB;

class ExpensePaymentController extends Controller
{

    use FinanceTrait, FilePaymentTrait;

    public function records($expense_id)
    {
        $records = ExpensePayment::where('expense_id', $expense_id)->get();

        return new ExpensePaymentCollection($records);
    }

    public function tables()
    {
        return [
            'expense_method_types' => ExpenseMethodType::all(),
            'payment_destinations' => $this->getPaymentDestinations()
        ];
    }

    public function Expense($expense_id)
    {
        $expense = Expense::find($expense_id);

        $total_paid = collect($expense->payments)->sum('payment');
        $total = $expense->total;
        $total_difference = round($total - $total_paid, 2);

        return [
            'number_full' => $expense->number_full,
            'total_paid' => $total_paid,
            'total' => $total,
            'total_difference' => $total_difference
        ];

    }


    public function store(ExpensePaymentRequest $request)
    {
        $id = $request->input('id');

        DB::connection('tenant')->transaction(function () use ($id, $request) {

            $record = ExpensePayment::firstOrNew(['id' => $id]);
            $record->fill($request->all());
            
            if($request->expense_method_type_id == 1){
                $request['payment_destination_id'] = 'cash';
            }

            $record->save();
            $this->createGlobalPayment($record, $request->all());
            $this->saveFiles($record, $request, 'expenses');

        });

        return [
            'success' => true,
            'message' => ($id)?'Pago editado con éxito':'Pago registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $item = ExpensePayment::findOrFail($id);
        $item->delete();

        return [
            'success' => true,
            'message' => 'Pago eliminado con éxito'
        ];
    }
 


}
