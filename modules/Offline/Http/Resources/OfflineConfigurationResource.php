<?php

namespace Modules\Offline\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfflineConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id, 
            'is_client'=> (bool) $this->is_client,
            'token_server'=> $this->token_server, 
            'url_server'=> $this->url_server,  
        ];
    }
}