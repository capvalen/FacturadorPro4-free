<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id, 
            'name' => $this->name,
            'pricing' => $this->pricing,
            'limit_documents' => $this->limit_documents,
            'limit_users' => $this->limit_users,
            // 'plan_documents' => $this->plan_documents,
            'plan_documents' => [],
            'locked' => $this->locked,
        ];
    }
}