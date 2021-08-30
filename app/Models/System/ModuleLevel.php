<?php

namespace App\Models\System;

use Hyn\Tenancy\Abstracts\SystemModel;

class ModuleLevel extends SystemModel
{

    protected $table = 'module_levels';

    protected $fillable = [
        'value',
        'description',
        'module_id'
    ];

}
