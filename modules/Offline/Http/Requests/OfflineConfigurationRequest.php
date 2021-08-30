<?php

namespace Modules\Offline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfflineConfigurationRequest extends FormRequest
{
    public function authorize() {
        return true;
    }
    
    public function rules() {
        $id = $this->input('id');
        
        return [
            'is_client' => ['required', 'boolean'],
            'token_server' => ['required_if:is_client, "true"'], 
            'url_server' => ['required_if:is_client, "true"'], 
        ];
    }
}