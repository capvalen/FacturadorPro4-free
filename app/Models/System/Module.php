<?php

namespace App\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use UsesSystemConnection;

    protected $fillable = [
        'description',
    ];

    public function levels()
    {
        return $this->hasMany(ModuleLevel::class, 'module_id');
    }
}
