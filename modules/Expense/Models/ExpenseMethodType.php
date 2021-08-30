<?php

namespace Modules\Expense\Models;
use App\Models\Tenant\ModelTenant;

class ExpenseMethodType extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'description',
        'has_card', 
    ];

    public function expense_payments()
    {
        return $this->hasMany(ExpensePayment::class,  'expense_method_type_id');
    }

    
    public function scopeWhereFilterPayments($query, $params)
    {

        return $query->with(['expense_payments' => function($q) use($params){
                    $q->whereBetween('date_of_payment', [$params->date_start, $params->date_end])
                        ->whereHas('associated_record_payment', function($p){
                            $p->whereStateTypeAccepted()->whereTypeUser();
                        });
                }]);

    }
}