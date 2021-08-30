<?php

namespace Modules\Finance\Helpers;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\PurchasePayment;
use Modules\Expense\Models\ExpensePayment;
use App\Models\Tenant\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ToPay
{

    public static function getToPay($request)
    {
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $supplier_id = $request['supplier_id'];
        $user = $request['user'];


        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        /*
         * Purchases
         */
        $purchase_payments = DB::table('purchase_payments')
            ->select('purchase_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('purchase_id');

        if($d_start && $d_end){
            // ->join('users', 'users.name', 'like', "%{$user}%")
            $purchases = DB::connection('tenant')
                ->table('purchases')
                // ->where('supplier_id', $supplier_id)
                ->where('user_id', $user)
                ->join('persons', 'persons.id', '=', 'purchases.supplier_id')
                ->leftJoinSub($purchase_payments, 'payments', function ($join) {
                    $join->on('purchases.id', '=', 'payments.purchase_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->whereIn('document_type_id', ['01','03','GU75', 'NE76'])
                ->select(DB::raw("purchases.id as id, ".
                                    "DATE_FORMAT(purchases.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                    "DATE_FORMAT(purchases.date_of_due, '%Y/%m/%d') as date_of_due, ".
                                    "persons.name as supplier_name, persons.id as supplier_id, purchases.document_type_id,".
                                    "CONCAT(purchases.series,'-',purchases.number) AS number_full, ".
                                    "purchases.total as total, ".
                                    "IFNULL(payments.total_payment, 0) as total_payment, ".
                                    "'purchase' AS 'type', ". "purchases.currency_type_id, " . "purchases.exchange_rate_sale"))
                ->where('purchases.establishment_id', $establishment_id)
                ->whereBetween('purchases.date_of_issue', [$d_start, $d_end]);

        }else{

            $purchases = DB::connection('tenant')
                ->table('purchases')
                // ->where('supplier_id', $supplier_id)
                ->where('user_id', $user)
                ->join('persons', 'persons.id', '=', 'purchases.supplier_id')
                ->leftJoinSub($purchase_payments, 'payments', function ($join) {
                    $join->on('purchases.id', '=', 'payments.purchase_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->whereIn('document_type_id', ['01','03','GU75', 'NE76'])
                ->select(DB::raw("purchases.id as id, ".
                                    "DATE_FORMAT(purchases.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                    "DATE_FORMAT(purchases.date_of_due, '%Y/%m/%d') as date_of_due, ".
                                    "persons.name as supplier_name, persons.id as supplier_id, purchases.document_type_id, ".
                                    "CONCAT(purchases.series,'-',purchases.number) AS number_full, ".
                                    "purchases.total as total, ".
                                    "IFNULL(payments.total_payment, 0) as total_payment, ".
                                    "'purchase' AS 'type', ". "purchases.currency_type_id, " . "purchases.exchange_rate_sale"))
                ->where('purchases.establishment_id', $establishment_id);

        }
        if ($supplier_id) {
            $purchases = $purchases->where('supplier_id', $supplier_id);
        }

        /*
         * Sale Notes
         */
        $expense_payments = DB::table('expense_payments')
            ->select('expense_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('expense_id');

        if($d_start && $d_end){

            $expenses = DB::connection('tenant')
                ->table('expenses')
                // ->where('supplier_id', $supplier_id)
                ->where('user_id', $user)
                ->join('persons', 'persons.id', '=', 'expenses.supplier_id')
                ->leftJoinSub($expense_payments, 'payments', function ($join) {
                    $join->on('expenses.id', '=', 'payments.expense_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->select(DB::raw("expenses.id as id, ".
                                "DATE_FORMAT(expenses.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                "null as date_of_due, ".
                                "persons.name as supplier_name, persons.id as supplier_id, null as document_type_id, ".
                                "expenses.number as number_full, ".
                                "expenses.total as total, ".
                                "IFNULL(payments.total_payment, 0) as total_payment, ".
                                "'expense' AS 'type', " . "expenses.currency_type_id, " . "expenses.exchange_rate_sale"))
                ->where('expenses.establishment_id', $establishment_id)
                // ->where('expenses.changed', false)
                ->whereBetween('expenses.date_of_issue', [$d_start, $d_end]);
                // ->where('expenses.total_canceled', false);

        }else{

            $expenses = DB::connection('tenant')
                ->table('expenses')
                // ->where('supplier_id', $supplier_id)
                ->where('user_id', $user)
                ->join('persons', 'persons.id', '=', 'expenses.supplier_id')
                ->leftJoinSub($expense_payments, 'payments', function ($join) {
                    $join->on('expenses.id', '=', 'payments.expense_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->select(DB::raw("expenses.id as id, ".
                                "DATE_FORMAT(expenses.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                "null as date_of_due, ".
                                "persons.name as supplier_name, persons.id as supplier_id, null as document_type_id, ".
                                "expenses.number as number_full, ".
                                "expenses.total as total, ".
                                "IFNULL(payments.total_payment, 0) as total_payment, ".
                                "'sale_note' AS 'type', " . "expenses.currency_type_id, " . "expenses.exchange_rate_sale"))
                ->where('expenses.establishment_id', $establishment_id);
                // ->where('expenses.changed', false)
                // ->where('expenses.total_canceled', false);
        }
        if ($supplier_id) {
            $expenses = $expenses->where('supplier_id', $supplier_id);
        }

        $records = $purchases->union($expenses)->get();

        return collect($records)->transform(function($row) {

                $total_to_pay = (float)$row->total - (float)$row->total_payment;
                $delay_payment = null;
                $date_of_due = null;

                if($total_to_pay > 0) {

                    if($row->date_of_due){
                        // dd($row->date_of_due);
                        $due =   Carbon::parse($row->date_of_due);
                        $date_of_due = Carbon::parse($row->date_of_due)->format('Y/m/d');
                        $now = Carbon::now();

                        if($now > $due){
                            $delay_payment = $now->diffInDays($due);
                        }


                    }
                }

                $guides = null;
                $date_payment_last = '';

                if($row->document_type_id){

                    $date_payment_last = PurchasePayment::where('purchase_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
                }
                else{
                    $date_payment_last = ExpensePayment::where('expense_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
                }

                return [
                    'id' => $row->id,
                    'date_of_issue' => $row->date_of_issue,
                    'supplier_name' => $row->supplier_name,
                    'supplier_id' => $row->supplier_id,
                    'number_full' => $row->number_full,
                    'total' => number_format((float) $row->total,2, ".", ""),
                    'total_to_pay' => number_format($total_to_pay,2, ".", ""),
                    'type' => $row->type,
                    'date_payment_last' => ($date_payment_last) ? $date_payment_last->date_of_payment->format('Y-m-d') : null,
                    'delay_payment' => $delay_payment,
                    'date_of_due' =>  $date_of_due,
                    'currency_type_id' => $row->currency_type_id,
                    'exchange_rate_sale' => (float)$row->exchange_rate_sale
                ];
        });
    }

    public static function getToPayNoFilter()
    {

        $purchase_payments = DB::table('purchase_payments')
            ->select('purchase_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('purchase_id');


            $purchases = DB::connection('tenant')
                ->table('purchases')
                ->join('persons', 'persons.id', '=', 'purchases.supplier_id')
                ->leftJoinSub($purchase_payments, 'payments', function ($join) {
                    $join->on('purchases.id', '=', 'payments.purchase_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->whereIn('document_type_id', ['01','03','GU75', 'NE76'])
                ->select(DB::raw("purchases.id as id, ".
                                    "DATE_FORMAT(purchases.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                    "DATE_FORMAT(purchases.date_of_due, '%Y/%m/%d') as date_of_due, ".
                                    "persons.name as supplier_name, persons.id as supplier_id, purchases.document_type_id, ".
                                    "CONCAT(purchases.series,'-',purchases.number) AS number_full, ".
                                    "purchases.total as total, ".
                                    "IFNULL(payments.total_payment, 0) as total_payment, ".
                                    "'purchase' AS 'type', ". "purchases.currency_type_id, " . "purchases.exchange_rate_sale"));


        $expense_payments = DB::table('expense_payments')
            ->select('expense_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('expense_id');

            $expenses = DB::connection('tenant')
                ->table('expenses')
                ->join('persons', 'persons.id', '=', 'expenses.supplier_id')
                ->leftJoinSub($expense_payments, 'payments', function ($join) {
                    $join->on('expenses.id', '=', 'payments.expense_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->select(DB::raw("expenses.id as id, ".
                                "DATE_FORMAT(expenses.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                "null as date_of_due, ".
                                "persons.name as supplier_name, persons.id as supplier_id, null as document_type_id, ".
                                "expenses.number as number_full, ".
                                "expenses.total as total, ".
                                "IFNULL(payments.total_payment, 0) as total_payment, ".
                                "'sale_note' AS 'type', " . "expenses.currency_type_id, " . "expenses.exchange_rate_sale"));


        $records = $purchases->union($expenses)->get();

        return collect($records)->transform(function($row) {

                $total_to_pay = (float)$row->total - (float)$row->total_payment;
                $delay_payment = null;
                $date_of_due = null;

                if($total_to_pay > 0) {

                    if($row->date_of_due){
                        // dd($row->date_of_due);
                        $due =   Carbon::parse($row->date_of_due);
                        $date_of_due = Carbon::parse($row->date_of_due)->format('Y-m-d');
                        $now = Carbon::now();

                        if($now > $due){
                            $delay_payment = $now->diffInDays($due);
                        }


                    }
                }

                $guides = null;
                $date_payment_last = '';

                if($row->document_type_id){

                    $date_payment_last = PurchasePayment::where('purchase_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
                }
                else{
                    $date_payment_last = ExpensePayment::where('expense_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
                }

                return [
                    'id' => $row->id,
                    'date_of_issue' => $row->date_of_issue,
                    'supplier_name' => $row->supplier_name,
                    'supplier_id' => $row->supplier_id,
                    'number_full' => $row->number_full,
                    'total' => number_format((float) $row->total,2, ".", ""),
                    'total_to_pay' => number_format($total_to_pay,2, ".", ""),
                    'type' => $row->type,
                    'date_payment_last' => ($date_payment_last) ? $date_payment_last->date_of_payment->format('Y-m-d') : null,
                    'delay_payment' => $delay_payment,
                    'date_of_due' =>  $date_of_due,
                    'currency_type_id' => $row->currency_type_id,
                    'exchange_rate_sale' => (float)$row->exchange_rate_sale
                ];
//            }
        });
    }

}
