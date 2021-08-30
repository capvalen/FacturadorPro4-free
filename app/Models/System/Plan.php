<?php

namespace App\Models\System;
use Hyn\Tenancy\Traits\UsesSystemConnection;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use UsesSystemConnection;


    protected $fillable = [
        'name',
        'pricing',
        'limit_users',
        'limit_documents',
        'plan_documents', 
        'locked', 
    ];

    public function setPlanDocumentsAttribute($value)
    {
        $this->attributes['plan_documents'] = (is_null($value))?null:json_encode($value);
    }

    public function getPlanDocumentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }


    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
