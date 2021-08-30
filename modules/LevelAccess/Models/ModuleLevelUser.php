<?php

namespace Modules\LevelAccess\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\Module;
use App\Models\Tenant\ModelTenant;

class ModuleLevelUser extends ModelTenant
{

    protected $fillable = [
        'module_user_id',
        'module_level_id',
    ];


    public function module_level() {

        return $this->belongsTo(ModuleLevel::class);

    }


}
