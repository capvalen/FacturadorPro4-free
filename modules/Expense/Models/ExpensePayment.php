<?php

namespace Modules\Expense\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\CardBrand;
use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Models\PaymentFile;

class ExpensePayment extends ModelTenant
{
    // protected $with = ['payment_method_type', 'card_brand'];
    public $timestamps = false;

    protected $fillable = [
        'expense_id',
        'date_of_payment',
        'expense_method_type_id',
        'has_card',
        'card_brand_id',
        'reference',
        'payment',
    ];

    protected $casts = [
        'date_of_payment' => 'date',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function expense_method_type()
    {
        return $this->belongsTo(ExpenseMethodType::class);
    }

    public function card_brand()
    {
        return $this->belongsTo(CardBrand::class);
    }

    public function global_payment()
    {
        return $this->morphOne(GlobalPayment::class, 'payment');
    }

    public function associated_record_payment()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function payment_file()
    {
        return $this->morphOne(PaymentFile::class, 'payment');
    }

}