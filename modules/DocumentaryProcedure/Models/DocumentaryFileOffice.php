<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryFileOffice extends ModelTenant
{
    protected $table = 'documentary_file_offices';

    protected $fillable = [
		'documentary_file_id',
		'documentary_office_id',
		'documentary_action_id',
		'observation',
		'status',
    ];
}
