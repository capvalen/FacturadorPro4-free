<?php

namespace Modules\BusinessTurn\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Document;
use App\Models\Tenant\Catalogs\IdentityDocumentType;

class DocumentHotel extends ModelTenant
{
    protected $fillable = [
        'document_id',
        'number',
        'name',
        'identity_document_type_id',
        'sex',
        'age',
        'civil_status',
        'nacionality',
        'origin',
        'room_number',
        'date_entry',
        'time_entry',
        'date_exit',
        'time_exit',
        'room_type',
        'ocupation',
        'guests'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

}
