<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryAction extends ModelTenant
{
	protected $table = 'documentary_actions';

	protected $fillable = ['description', 'active', 'name'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
