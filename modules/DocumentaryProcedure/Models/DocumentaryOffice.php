<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryOffice extends ModelTenant
{
	protected $table = 'documentary_offices';

	protected $fillable = ['description', 'active', 'name'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
