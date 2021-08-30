<?php

namespace App\Models\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Modules\LevelAccess\Models\ModuleLevel;

class Module extends ModelTenant
{
    use UsesTenantConnection;

    protected $with = ['levels'];

    protected $fillable = [
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    public function levels()
    {
        return $this->hasMany(ModuleLevel::class);
    }
}