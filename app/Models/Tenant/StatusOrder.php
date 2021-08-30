<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\Order;

class StatusOrder extends ModelTenant
{
  public function order()
  {
      return $this->hasMany(Order::class);
  }
}
