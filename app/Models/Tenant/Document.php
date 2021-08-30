<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;
use Modules\BusinessTurn\Models\DocumentHotel;
use Modules\BusinessTurn\Models\DocumentTransport;
use Modules\Order\Models\OrderNote;


class Document extends ModelTenant
{
    protected $with = ['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'invoice', 'note', 'payments'];

    protected $fillable = [
        'user_id',
        'external_id',
        'establishment_id',
        'establishment',
        'soap_type_id',
        'state_type_id',
        'ubl_version',
        'group_id',
        'document_type_id',
        'series',
        'number',
        'date_of_issue',
        'time_of_issue',
        'customer_id',
        'customer',
        'currency_type_id',
        'purchase_order',
        'quotation_id',
        'exchange_rate_sale',
        'total_prepayment',
        'total_discount',
        'total_charge',
        'total_exportation',
        'total_free',
        'total_taxed',
        'total_unaffected',
        'total_exonerated',
        'total_igv',
        'total_base_isc',
        'total_isc',
        'total_base_other_taxes',
        'total_other_taxes',
        'total_taxes',
        'total_value',
        'total',
        'charges',
        'discounts',
        'prepayments',
        'guides',
        'related',
        'perception',
        'detraction',
        'legends',
        'additional_information',
        'filename',
        'hash',
        'qr',
        'has_xml',
        'has_pdf',
        'has_cdr',
        'has_prepayment',
        'affectation_type_prepayment',
        'data_json',
        'send_server',
        'shipping_status',
        'sunat_shipping_status',
        'query_status',
        'total_plastic_bag_taxes',
        'sale_note_id',
        'success_shipping_status',
        'success_sunat_shipping_status',
        'success_query_status',
        'plate_number',
        'total_canceled',
        'order_note_id',
        'soap_shipping_response',
        'pending_amount_prepayment',
        'payment_method_type_id',
        'regularize_shipping',
        'response_regularize_shipping',
        'seller_id',
        'reference_data',
        'terms_condition',
        'payment_condition_id',
        'is_editable',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];

    public function getEstablishmentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    }

    public function getCustomerAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = (is_null($value))?null:json_encode($value);
    }

    public function getChargesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setChargesAttribute($value)
    {
        $this->attributes['charges'] = (is_null($value))?null:json_encode($value);
    }

    public function getDiscountsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDiscountsAttribute($value)
    {
        $this->attributes['discounts'] = (is_null($value))?null:json_encode($value);
    }

    public function getPrepaymentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPrepaymentsAttribute($value)
    {
        $this->attributes['prepayments'] = (is_null($value))?null:json_encode($value);
    }

    public function getGuidesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setGuidesAttribute($value)
    {
        $this->attributes['guides'] = (is_null($value))?null:json_encode($value);
    }

    public function getRelatedAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setRelatedAttribute($value)
    {
        $this->attributes['related'] = (is_null($value))?null:json_encode($value);
    }

    public function getPerceptionAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPerceptionAttribute($value)
    {
        $this->attributes['perception'] = (is_null($value))?null:json_encode($value);
    }

    public function getDetractionAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDetractionAttribute($value)
    {
        $this->attributes['detraction'] = (is_null($value))?null:json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = (is_null($value))?null:json_encode($value);
    }

    public function getDataJsonAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDataJsonAttribute($value)
    {
        $this->attributes['data_json'] = (is_null($value))?null:json_encode($value);
    }


    public function getSoapShippingResponseAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSoapShippingResponseAttribute($value)
    {
        $this->attributes['soap_shipping_response'] = (is_null($value))?null:json_encode($value);
    }

    public function getResponseRegularizeShippingAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setResponseRegularizeShippingAttribute($value)
    {
        $this->attributes['response_regularize_shipping'] = (is_null($value))?null:json_encode($value);
    }

    public function getAdditionalInformationAttribute($value)
    {
        $arr = explode('|', $value);
        return $arr;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function person() {
        return $this->belongsTo(Person::class, 'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }

    public function getCompanyAttribute()
    {
        return Company::first();
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function note()
    {
        return $this->hasOne(Note::class);
    }

    public function items()
    {
        return $this->hasMany(DocumentItem::class);
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    public function payments()
    {
        return $this->hasMany(DocumentPayment::class);
    }

    public function fee()
    {
        return $this->hasMany(DocumentFee::class);
    }

    public function inventory_kardex()
    {
        return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class, 'sale_note_id');
    }

    public function hotel()
    {
        return $this->hasOne(DocumentHotel::class);
    }

    public function transport()
    {
        return $this->hasOne(DocumentTransport::class);
    }

    public function getNumberFullAttribute()
    {
        return $this->series.'-'.$this->number;
    }

    public function getNumberToLetterAttribute()
    {
        $legends = $this->legends;
        $legend = collect($legends)->where('code', '1000')->first();
        return $legend->value;
    }

    public function getDownloadExternalXmlAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'document', 'type' => 'xml', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalPdfAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'document', 'type' => 'pdf', 'external_id' => $this->external_id]);
    }

    public function getDownloadExternalCdrAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'document', 'type' => 'cdr', 'external_id' => $this->external_id]);
    }


    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'admin') ? null : $query->where('user_id', $user->id)->orWhere('seller_id', $user->id)->latest();
        // return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }

    public function scopeWhereNotSent($query)
    {
        return  $query->whereIn('state_type_id', ['01','03'])->where('date_of_issue','<=',date('Y-m-d'));
    }

    public function affected_documents()
    {
        return $this->hasMany(Note::class, 'affected_document_id');
    }

    public function scopeWhereHasPrepayment($query)
    {
        return $query->where([['has_prepayment', true],['was_deducted_prepayment', false],['state_type_id','05']]);
    }

    public function reference_guides()
    {
        return $this->hasMany(Dispatch::class, 'reference_document_id', 'id');
    }

    public function summary_document()
    {
        return $this->hasOne(SummaryDocument::class);
    }

    public function scopeWhereAffectationTypePrepayment($query, $type)
    {
        return $query->where('affectation_type_prepayment', $type);
    }

    public function scopeWhereStateTypeAccepted($query)
    {
        return $query->whereIn('state_type_id', ['01','03','05','07','13']);
    }

    public function payment_method_type()
    {
        return $this->belongsTo(PaymentMethodType::class);
    }

    public function scopeWhereRegularizeShipping($query)
    {
        return  $query->where('state_type_id', '01')->where('regularize_shipping', true);
    }

    public function order_note()
    {
        return $this->belongsTo(OrderNote::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsEditableAttribute($value)
    {
        return $value ? true : false;
    }

    /**
     * Evalua si es posible borrarlo basado en las condiciones:
     *
     * regularize_shipping y response_regularize_shipping no este vacio
     *
     * El documento este replicado  en series y numero
     *
     *
     * @return bool
     */
    public function canDelete()
    {
        if (!empty($this->regularize_shipping) &&
            !empty($this->response_regularize_shipping)) {
            $duplicated = self::where([
                'series' => $this->series ,
                'number' => $this->number ,
            ])->where('id', '!=', $this->id)->first();
            if (!empty($duplicated)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Devuelve el ultimo numero por serie, si no existe devielve 0
     *
     * @param string $serie
     *
     * @return int
     */
    public static function getLastNumberBySerie($serie){
        $t = Document::where('series',$serie)->select('number')->orderby('number','DESC')->first();
        if(!empty($t)){
            return $t->number;
        }
        return 0;
    }
}
