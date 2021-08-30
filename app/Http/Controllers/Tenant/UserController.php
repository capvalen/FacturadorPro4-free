<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\User;
use App\Models\Tenant\Module;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Establishment;
use App\Http\Requests\Tenant\UserRequest;
use App\Http\Resources\Tenant\UserResource;
use Modules\LevelAccess\Models\ModuleLevel;
use App\Http\Resources\Tenant\UserCollection;

class UserController extends Controller
{
    public function index()
    {
        return view('tenant.users.index');
    }

    public function record($id)
    {
        $record = new UserResource(User::findOrFail($id));

        return $record;
    }

    private function prepareModules(Module $module): Module
    {
        $levels = [];
        foreach ($module->levels as $level) {
            array_push($levels, [
                'id' => "{$module->id}-{$level->id}",
                'description' => $level->description,
                'module_id' => $level->module_id,
                'is_parent' => false,
            ]);
        }
        unset($module->levels);
        $module->is_parent = true;
        $module->childrens = $levels;
        return $module;
    }

    public function tables()
    {
        $modulesTenant = DB::connection('tenant')
            ->table('module_user')
            ->where('user_id', 1)
            ->select('module_id')
            ->get()
            ->pluck('module_id')
            ->all();

        $levelsTenant = DB::connection('tenant')
            ->table('module_level_user')
            ->where('user_id', 1)
            ->get()
            ->pluck('module_level_id')
            ->toArray();

        $modules = Module::with(['levels' => function ($query) use ($levelsTenant) {
            $query->whereIn('id', $levelsTenant);
        }])
            ->orderBy('order_menu')
            ->whereIn('id', $modulesTenant)
            ->get()
            ->each(function ($module) {
                return $this->prepareModules($module);
            });

        $establishments = Establishment::orderBy('description')->get();
        $types = [['type' => 'admin', 'description' => 'Administrador'], ['type' => 'seller', 'description' => 'Vendedor']];

        return compact('modules', 'establishments', 'types');
    }

    public function store(UserRequest $request)
    {
        $id = $request->input('id');

        if (!$id) { //VALIDAR EMAIL DISPONIBLE
            $verify = User::where('email', $request->input('email'))->first();
            if ($verify) {
                return [
                    'success' => false,
                    'message' => 'Email no disponible. Ingrese otro Email'
                ];
            }
        }

        $user = User::firstOrNew(['id' => $id]);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->establishment_id = $request->input('establishment_id');
        $user->type = $request->input('type');
        if (!$id) {
            $user->api_token = str_random(50);
            $user->password = bcrypt($request->input('password'));
        } elseif ($request->has('password')) {
            if (config('tenant.password_change')) {
                $user->password = bcrypt($request->input('password'));
            }
        }
        $user->save();

        if ($user->id != 1) {
            $array_modules = [];
            $array_levels = [];
            DB::connection('tenant')->table('module_user')->where('user_id', $user->id)->delete();
            DB::connection('tenant')->table('module_level_user')->where('user_id', $user->id)->delete();
            foreach ($request->modules as $module) {
                array_push($array_modules, [
                    'module_id' => $module, 'user_id' => $user->id
                ]);
            }
            foreach ($request->levels as $level) {
                array_push($array_levels, [
                    'module_level_id' => $level, 'user_id' => $user->id
                ]);
            }
            DB::connection('tenant')->table('module_user')->insert($array_modules);
            DB::connection('tenant')->table('module_level_user')->insert($array_levels);
        }

        return [
            'success' => true,
            'message' => ($id) ? 'Usuario actualizado' : 'Usuario registrado'
        ];
    }

    public function records()
    {
        $records = User::all();

        return new UserCollection($records);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return [
            'success' => true,
            'message' => 'Usuario eliminado con éxito'
        ];
    }
}
