<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\Module;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class UserResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request)
	{
        $modules = DB::connection('tenant')
            ->table('module_user')
            ->select('module_id')
            ->where('user_id', $this->id)
            ->get()
            ->pluck('module_id')
            ->toArray();
		$levels = DB::connection('tenant')
            ->table('module_level_user')
            ->select('module_level_id')
            ->where('user_id', $this->id)
            ->get()
            ->pluck('module_level_id')
            ->toArray();

		return [
			'id'               => $this->id,
			'email'            => $this->email,
			'name'             => $this->name,
			'api_token'        => $this->api_token,
			'establishment_id' => $this->establishment_id,
			'type'             => $this->type,
			'modules'          => $modules,
			'levels'           => $levels,
			'locked'           => (bool) $this->locked,
		];
	}
}
