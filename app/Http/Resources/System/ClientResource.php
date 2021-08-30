<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\System\Module;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        // $all_modules = Module::orderBy('description')->get();
        // $modules_in_user = $this->modules->pluck('module_id')->toArray();
        // dd($all_modules,$modules_in_user);
        // $modules = [];
        // foreach ($all_modules as $module)
        // {
        //     $modules[] = [
        //         'id' => $module->id,
        //         'description' => $module->description,
        //         'checked' => (bool) in_array($module->id, $modules_in_user)
        //     ];
        // }

        return [
            'id' => $this->id,
                'hostname' => $this->hostname->fqdn,
                'name' => $this->name,
                'email' => $this->email,
                'token' => $this->token,
                'number' => $this->number,
                'plan_id' => $this->plan_id,
                'locked' => (bool) $this->locked,
                'locked_emission' => (bool) $this->locked_emission,
                'modules' => $this->modules,
                'levels' => $this->levels,
                //'count_doc' => $this->count_doc,
               // 'max_documents' => (int) $this->plan->limit_documents,
                //'count_user' => $this->count_user,
                //'max_users' => (int) $this->plan->limit_users,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

                'soap_send_id' => $this->soap_send_id,
                'soap_type_id' => $this->soap_type_id,
                'soap_username' => $this->soap_username,
                'soap_password' => $this->soap_password,
                'soap_url' => $this->soap_url,
                'config_system_env' => (bool)$this->config_system_env,
                'certificate' => $this->certificate,
            'smtp_host'=>$this->smtp_host,
            'smtp_port'=>$this->smtp_port,
            'smtp_user'=>$this->smtp_user,
            'smtp_password'=>null, // dont show smtp password
            'smtp_encryption'=>$this->smtp_encryption,

        ];

    }
}
