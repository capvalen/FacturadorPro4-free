<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Cash;
use App\Models\Tenant\BankAccount;
use App\Models\Tenant\SoapType;
use Modules\Sale\Models\QuotationPayment;
use Modules\Expense\Models\ExpensePayment;
use App\Models\Tenant\{DocumentPayment, SaleNote, SaleNotePayment, PurchasePayment, User};
use Modules\Sale\Models\ContractPayment;
use Modules\Pos\Models\CashTransaction;
use Modules\Sale\Models\TechnicalServicePayment;

class GlobalPayment extends ModelTenant
{

    protected $fillable = [
        'soap_type_id',
        'destination_id',
        'destination_type',
        'payment_id',
        'payment_type',
        'user_id',
    ];


    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function destination()
    {
        return $this->morphTo();
    }


    public function payment()
    {
        return $this->morphTo();
    }

    public function doc_payments()
    {
        return $this->belongsTo(DocumentPayment::class, 'payment_id')
                    ->wherePaymentType(DocumentPayment::class);
    }
    public function exp_payment()
    {
        return $this->belongsTo(ExpensePayment::class, 'payment_id')
                    ->wherePaymentType(ExpensePayment::class);
    }

    public function sln_payments()
    {
        return $this->belongsTo(SaleNotePayment::class, 'payment_id')
                    ->wherePaymentType(SaleNotePayment::class);
    }

    public function pur_payment()
    {
        return $this->belongsTo(PurchasePayment::class, 'payment_id')
                    ->wherePaymentType(PurchasePayment::class);
    }

    public function quo_payment()
    {
        return $this->belongsTo(QuotationPayment::class, 'payment_id')
                    ->wherePaymentType(QuotationPayment::class);
    }

    public function con_payment()
    {
        return $this->belongsTo(ContractPayment::class, 'payment_id')
                    ->wherePaymentType(ContractPayment::class);
    }

    public function inc_payment()
    {
        return $this->belongsTo(IncomePayment::class, 'payment_id')
                    ->wherePaymentType(IncomePayment::class);
    }

    public function cas_transaction()
    {
        return $this->belongsTo(CashTransaction::class, 'payment_id')
                    ->wherePaymentType(CashTransaction::class);
    }

    public function tec_serv_payment()
    {
        return $this->belongsTo(TechnicalServicePayment::class, 'payment_id')
                    ->wherePaymentType(TechnicalServicePayment::class);
    }

    public function getDestinationDescriptionAttribute()
    {
        return $this->destination_type === Cash::class ? 'CAJA GENERAL': "{$this->destination->bank->description} - {$this->destination->currency_type_id} - {$this->destination->description}";
    }

    public function getTypeRecordAttribute()
    {
        return $this->destination_type === Cash::class ? 'cash':'bank_account';
    }

    public function getInstanceTypeAttribute()
    {
        $instance_type = [
            DocumentPayment::class => 'document',
            SaleNotePayment::class => 'sale_note',
            PurchasePayment::class => 'purchase',
            ExpensePayment::class => 'expense',
            QuotationPayment::class => 'quotation',
            ContractPayment::class => 'contract',
            IncomePayment::class => 'income',
            CashTransaction::class => 'cash_transaction',
            TechnicalServicePayment::class => 'technical_service',
        ];

        return $instance_type[$this->payment_type];
    }

    public function getInstanceTypeDescriptionAttribute()
    {

        $description = null;

        switch ($this->instance_type) {
            case 'document':
                $description = 'CPE';
                break;
            case 'sale_note':
                $description = 'NOTA DE VENTA';
                break;
            case 'purchase':
                $description = 'COMPRA';
                break;
            case 'expense':
                $description = 'GASTO';
                break;
            case 'quotation':
                $description = 'COTIZACIÓN';
                break;
            case 'contract':
                $description = 'CONTRATO';
                break;
            case 'income':
                $description = 'INGRESO';
                break;
            case 'cash_transaction':
                $description = 'INGRESO';
                break;
            case 'technical_service':
                $description = 'SERVICIO TÉCNICO';
                break;
        }

        return $description;
    }


    public function getTypeMovementAttribute()
    {
        $type = null;

        switch ($this->instance_type) {

            case 'document':
            case 'sale_note':
            case 'quotation':
            case 'contract':
            case 'income':
            case 'cash_transaction':
            case 'technical_service':
                $type = 'input';
                break;
            case 'purchase':
            case 'expense':
                $type = 'output';
                break;

        }

        return $type;

    }


    public function getDataPersonAttribute(){

        $record = $this->payment->associated_record_payment;

        switch ($this->instance_type) {

            case 'document':
            case 'sale_note':
            case 'quotation':
            case 'contract':
            case 'technical_service':
                $person['name'] = $record->customer->name;
                $person['number'] = $record->customer->number;
                break;
            case 'purchase':
            case 'expense':
                $person['name'] = $record->supplier->name;
                $person['number'] = $record->supplier->number;
                break;
            case 'income':
                $person['name'] = $record->customer;
                $person['number'] = '';
            case 'cash_transaction':
                $person['name'] = '-';
                $person['number'] = '';
        }

        return (object) $person;
    }


    public function scopeWhereFilterPaymentType($query, $params)
    {

        return $query->whereHas('doc_payments', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser();
                        });

                })
                ->OrWhereHas('exp_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser();
                        });

                })
                ->OrWhereHas('sln_payments', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser()
                                ->whereNotChanged();
                        });

                })
                ->OrWhereHas('pur_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser();
                        });

                })
                ->OrWhereHas('quo_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser()
                                ->whereNotChanged();
                        });

                })
                ->OrWhereHas('con_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser()
                                ->whereNotChanged();
                        });

                })
                ->OrWhereHas('inc_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser();
                        });

                })
                ->OrWhereHas('cas_transaction', function($q) use($params){
                    $q->whereBetween('date', [$params->date_start, $params->date_end]);
                })
                ->OrWhereHas('tec_serv_payment', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereTypeUser();
                        });

                });

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereDefinePaymentType($query, $payment_type)
    {

        if($payment_type === IncomePayment::class){
            return $query->whereIn('payment_type', [CashTransaction::class, $payment_type]);
        }

        return $query->wherePaymentType($payment_type);

    }
}
