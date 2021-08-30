<?php

namespace Modules\LevelAccess\Http\ViewComposers;

use Illuminate\Support\Facades\DB;
use Modules\LevelAccess\Models\ModuleLevel;

class ModuleLevelViewComposer
{
    public function compose($view)
    {
        $user = auth()->user();
        $myLevels = DB::connection('tenant')
            ->table('module_level_user')
            ->select('module_level_id')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('module_level_id')
            ->toArray();

        $module_levels = ModuleLevel::whereIn('id', $myLevels)
            ->get()
            ->pluck('value')
            ->toArray();
        if(count($module_levels) > 0) {
            $view->vc_module_levels = $module_levels;
        } else {
            $view->vc_module_levels = ModuleLevel::all()->pluck('value')->toArray();
        }
    }
}
