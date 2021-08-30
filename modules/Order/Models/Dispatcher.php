<?php

namespace Modules\Order\Models;

use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\ModelTenant;

class Dispatcher extends ModelTenant
{

    protected $with = ['identity_document_type'];
 
    protected $fillable = [
        'identity_document_type_id',
        'number',
        'name',
        'address',
    ];


    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

}
