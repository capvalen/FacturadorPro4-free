<?php

namespace Modules\DocumentaryProcedure\Models;

use App\Models\Tenant\ModelTenant;

class DocumentaryFile extends ModelTenant
{
	protected $table = 'documentary_files';

	protected $fillable = [
		'documentary_document_id',
		'documentary_process_id',
		'number',
		'year',
		'invoice',
		'date_register',
		'time_register',
		'person_id',
		'sender',
		'subject',
		'attached_file',
		'observation',
	];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}

    public function getSenderAttribute($value)
	{
		return (is_null($value)) ? null : (object) json_decode($value);
	}

	public function setSenderAttribute($value)
	{
		$this->attributes['sender'] = (is_null($value)) ? null : json_encode($value);
	}

    public function offices()
    {
        return $this->hasMany(DocumentaryFileOffice::class, 'documentary_file_id');
    }
}
