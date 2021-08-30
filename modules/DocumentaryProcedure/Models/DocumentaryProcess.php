<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryProcess extends ModelTenant
{
	protected $table = 'documentary_processes';

	protected $fillable = ['description', 'active', 'name'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
