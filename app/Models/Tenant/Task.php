<?php

namespace App\Models\Tenant;

class Task extends ModelTenant
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['class', 'execution_time', 'output'];
}
