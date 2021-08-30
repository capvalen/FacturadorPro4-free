<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model; 

class PlanDocument extends Model
{ 

    protected $table = "plan_documents";
    
    protected $fillable = [
        'description', 
    ];

    public $timestamps = false;
}
