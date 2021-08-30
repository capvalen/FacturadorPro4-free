<?php

namespace App\Services;
use App\Models\Tenant\ConfigurationEcommerce;

class UserAdminService
{

    public function getUserAdmin()
    {
        return ConfigurationEcommerce::first();
    }


}
