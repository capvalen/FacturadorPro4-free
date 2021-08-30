<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\SoapType;
use App\Models\Tenant\StateType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\ModelTenant;
use Modules\Inventory\Models\InventoryKardex;

class Devolution extends ModelTenant
{

    protected $fillable = [
        'user_id',
        'external_id',
        'prefix',
        'establishment_id',
        'soap_type_id',
        'state_type_id',
        'date_of_issue',
        'time_of_issue',
        'devolution_reason_id',
        'observation',
        'filename',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];


    public function getNumberFullAttribute()
    {
        return $this->prefix.'-'.$this->id;
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function devolution_reason()
    {
        return $this->belongsTo(DevolutionReason::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function items()
    {
        return $this->hasMany(DevolutionItem::class);
    }

    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }
    
    public function inventory_kardex()
    {
        return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    }

}
