<?php

namespace Modules\Expense\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\SoapType;
use App\Models\Tenant\StateType;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\CurrencyType;
use App\Models\Tenant\ModelTenant;

class Expense extends ModelTenant
{

    // protected $with = ['user', 'items'];

    protected $fillable = [
        'user_id',
        'soap_type_id',
        'expense_type_id',
        'expense_reason_id',
        'establishment_id',
        'supplier_id',
        'currency_type_id',
        'external_id',
        'state_type_id',
        'number',
        'date_of_issue',
        'time_of_issue',
        'supplier',
        'exchange_rate_sale',
        'total',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];

    public function getSupplierAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSupplierAttribute($value)
    {
        $this->attributes['supplier'] = (is_null($value))?null:json_encode($value);
    }

    public function supplier() {
        return $this->belongsTo(Person::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expense_reason()
    {
        return $this->belongsTo(ExpenseReason::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }

    public function expense_type()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function payments()
    {
        return $this->hasMany(ExpensePayment::class);
    }

    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }

    public function getNumberFullAttribute()
    {
        return $this->number;
    }

    public function document_type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function scopeWhereStateTypeAccepted($query)
    {
        return $query->whereIn('state_type_id', ['01','03','05','07','13']);
    }
}
