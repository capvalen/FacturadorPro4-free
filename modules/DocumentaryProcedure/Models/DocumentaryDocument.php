<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryDocument extends ModelTenant
{
	protected $table = 'documentary_documents';

	protected $fillable = ['description', 'active', 'name'];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}
}
