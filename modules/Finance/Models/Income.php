<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\SoapType;
use App\Models\Tenant\StateType;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\CurrencyType;
use App\Models\Tenant\ModelTenant;

class Income extends ModelTenant
{

    protected $table = 'income';
    // protected $with = ['user', 'items'];

    protected $fillable = [
        'user_id',
        'soap_type_id',
        'income_type_id',
        'income_reason_id',
        'establishment_id',
        'customer',
        'currency_type_id',
        'external_id',
        'state_type_id',
        'number',
        'date_of_issue',
        'time_of_issue',
        'exchange_rate_sale',
        'total',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];
  
    public function items()
    {
        return $this->hasMany(IncomeItem::class);
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

    public function income_reason()
    {
        return $this->belongsTo(IncomeReason::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }

    public function income_type()
    {
        return $this->belongsTo(IncomeType::class);
    }

    public function payments()
    {
        return $this->hasMany(IncomePayment::class);
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
        return $this->belongsTo(IncomeType::class, 'income_type_id');
    }

    public function scopeWhereStateTypeAccepted($query)
    {
        return $query->whereIn('state_type_id', ['01','03','05','07','13']);
    }
}
