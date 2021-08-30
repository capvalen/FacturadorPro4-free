<?php

namespace Modules\Sale\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\ModelTenant;

class UserCommission extends ModelTenant
{

    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'type', 

    ];
 
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }

}
