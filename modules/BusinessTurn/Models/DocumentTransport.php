<?php

namespace Modules\BusinessTurn\Models;
 
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Document;
use App\Models\Tenant\Catalogs\IdentityDocumentType;

class DocumentTransport extends ModelTenant
{
    protected $fillable = [
        'document_id',
        'seat_number',
        'passenger_manifest',
        'identity_document_type_id',
        'number_identity_document',
        'passenger_fullname',
        'origin_district_id',
        'origin_address',
        'destinatation_district_id',
        'destinatation_address',
        'start_date',
        'start_time', 
    ];
  
    public function getOriginDistrictIdAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOriginDistrictIdAttribute($value)
    {
        $this->attributes['origin_district_id'] = (is_null($value))?null:json_encode($value);
    }

    public function getDestinatationDistrictIdAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDestinatationDistrictIdAttribute($value)
    {
        $this->attributes['destinatation_district_id'] = (is_null($value))?null:json_encode($value);
    }


    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

}