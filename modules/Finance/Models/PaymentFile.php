<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\{
    DocumentPayment,
    SaleNotePayment,
    ModelTenant
};

class PaymentFile extends ModelTenant
{

    public $timestamps = false;
    
    protected $fillable = [
        'filename',
        'payment_id',
        'payment_type', 
    ];
 
 
    public function payment()
    {
        return $this->morphTo();
    }

    public function doc_payments()
    {
        return $this->belongsTo(DocumentPayment::class, 'payment_id')
                    ->wherePaymentType(DocumentPayment::class);
    } 
    
    public function sln_payments()
    {
        return $this->belongsTo(SaleNotePayment::class, 'payment_id')
                    ->wherePaymentType(SaleNotePayment::class);
    }
 
 
    public function getInstanceTypeAttribute()
    {
        $instance_type = [
            DocumentPayment::class => 'document',
            SaleNotePayment::class => 'sale_note', 
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
             
        } 

        return $description;
    }

    public function getDataPersonAttribute(){

        $record = $this->payment->associated_record_payment;

        switch ($this->instance_type) {

            case 'document':
            case 'sale_note':
                $person['name'] = $record->customer->name;
                $person['number'] = $record->customer->number;
                break; 

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
                ->OrWhereHas('sln_payments', function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser()
                                ->whereNotChanged();
                        });
                    
                });

    }

}