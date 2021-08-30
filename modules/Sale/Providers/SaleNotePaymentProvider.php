<?php

namespace Modules\Sale\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;

use Exception;

class SaleNotePaymentProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->payments(); 
    }
 
    

    private function payments()
    {

        SaleNotePayment::created(function ($sale_note_payment) {
            $this->transaction_payment($sale_note_payment);
        });
 
        // SaleNotePayment::deleted(function ($sale_note_payment) {
        //     $this->transaction_payment($sale_note_payment);
        // });
        
    }
 
    private function transaction_payment($sale_note_payment){

        $sale_note = $sale_note_payment->associated_record_payment;

        $total_payments = $sale_note->payments->sum('payment');

        $balance = $sale_note->total - $total_payments;

        if($balance <= 0){

            $sale_note->total_canceled = true;
            $sale_note->update();

        }else{
            
            $sale_note->total_canceled = false;
            $sale_note->update();
        }

    }
}
