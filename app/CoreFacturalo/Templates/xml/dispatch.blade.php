{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2"
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>{{ $document->series }}-{{ $document->number }}</cbc:ID>
    <cbc:IssueDate>{{ $document->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
    <cbc:IssueTime>{{ $document->time_of_issue }}</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode>{{ $document->document_type_id }}</cbc:DespatchAdviceTypeCode>
    @if($document->observations)
    <cbc:Note><![CDATA[{{ $document->observations }}]]></cbc:Note>
    @endif
    {{--{% if doc.docBaja -%}--}}
    {{--<cac:OrderReference>--}}
        {{--<cbc:ID>{{ doc.docBaja.nroDoc }}</cbc:ID>--}}
        {{--<cbc:OrderTypeCode listAgencyName="PE:SUNAT"--}}
                           {{--listName="SUNAT:Identificador de Tipo de Documento"--}}
                           {{--listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">{{ doc.docBaja.tipoDoc }}</cbc:OrderTypeCode>--}}
    {{--</cac:OrderReference>--}}
    {{--{% endif -%}--}}
    @if($document->related)
    <cac:AdditionalDocumentReference>
        <cbc:ID>{{ $document->related->number }}</cbc:ID>
        <cbc:DocumentTypeCode listAgencyName="PE:SUNAT"
                              listName="SUNAT:Identificador de documento relacionado"
                              listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo21">{{ $document->related->document_type_id }}</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>
    @endif
    <cac:Signature>
        <cbc:ID>{{ config('configuration.signature_uri') }}</cbc:ID>
        <cbc:Note>{{ config('configuration.signature_note') }}</cbc:Note>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>{{ $company->number }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[{{ $company->trade_name }}]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#{{ config('configuration.signature_uri') }}</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:DespatchSupplierParty>
        <cbc:CustomerAssignedAccountID schemeID="6">{{ $company->number }}</cbc:CustomerAssignedAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $company->name }}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DespatchSupplierParty>
    <cac:DeliveryCustomerParty>
        <cbc:CustomerAssignedAccountID schemeID="{{ $document->customer->identity_document_type_id }}">{{ $document->customer->number }}</cbc:CustomerAssignedAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $document->customer->name }}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DeliveryCustomerParty>
    {{--{% if doc.tercero -%}--}}
    {{--<cac:SellerSupplierParty>--}}
        {{--<cbc:CustomerAssignedAccountID schemeID="{{ doc.tercero.tipoDoc }}">{{ doc.tercero.numDoc }}</cbc:CustomerAssignedAccountID>--}}
        {{--<cac:Party>--}}
            {{--<cac:PartyLegalEntity>--}}
                {{--<cbc:RegistrationName><![CDATA[{{ doc.tercero.rznSocial|raw }}]]></cbc:RegistrationName>--}}
            {{--</cac:PartyLegalEntity>--}}
        {{--</cac:Party>--}}
    {{--</cac:SellerSupplierParty>--}}
    {{--{% endif -%}--}}
    <cac:Shipment>
        <cbc:ID>1</cbc:ID>
        <cbc:HandlingCode>{{ $document->transfer_reason_type_id }}</cbc:HandlingCode>
        @if($document->transfer_reason_description)
        <cbc:Information>{{ $document->transfer_reason_description }}</cbc:Information>
        @endif
        <cbc:GrossWeightMeasure unitCode="{{ $document->unit_type_id }}">{{ $document->total_weight }}</cbc:GrossWeightMeasure>
        @if($document->packages_number)
        <cbc:TotalTransportHandlingUnitQuantity>{{ $document->packages_number }}</cbc:TotalTransportHandlingUnitQuantity>
        @endif
        <cbc:SplitConsignmentIndicator>{{ ($document->transshipment_indicator)?"true":"false" }}</cbc:SplitConsignmentIndicator>
        <cac:ShipmentStage>
            <cbc:TransportModeCode>{{ $document->transport_mode_type_id }}</cbc:TransportModeCode>
            <cac:TransitPeriod>
                <cbc:StartDate>{{ $document->date_of_shipping->format('Y-m-d') }}</cbc:StartDate>
            </cac:TransitPeriod>
            @if($document->dispatcher)
            @php($dispatcher = $document->dispatcher)
            <cac:CarrierParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="{{ $dispatcher->identity_document_type_id }}">{{ $dispatcher->number }}</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name><![CDATA[{{ $dispatcher->name }}]]></cbc:Name>
                </cac:PartyName>
            </cac:CarrierParty>
            @endif
            @if($document->driver)
            @php($driver = $document->driver)
            <cac:TransportMeans>
                <cac:RoadTransport>
                    <cbc:LicensePlateID>{{ $document->license_plate }}</cbc:LicensePlateID>
                </cac:RoadTransport>
            </cac:TransportMeans>
            <cac:DriverPerson>
                <cbc:ID schemeID="{{ $driver->identity_document_type_id }}">{{ $driver->number }}</cbc:ID>
            </cac:DriverPerson>
            @endif
        </cac:ShipmentStage>
        <cac:Delivery>
            <cac:DeliveryAddress>
                <cbc:ID>{{ $document->delivery->location_id }}</cbc:ID>
                <cbc:StreetName>{{ $document->delivery->address }}</cbc:StreetName>
            </cac:DeliveryAddress>
        </cac:Delivery>
        @if($document->container_number)
        <cac:TransportHandlingUnit>
            <cbc:ID>{{ $document->container_number }}</cbc:ID>
        </cac:TransportHandlingUnit>
        @endif
        @if($document->secondary_license_plates)
            @php($secondary_license_plates = $document->secondary_license_plates)
            @if($secondary_license_plates->semitrailer)
            <cac:TransportHandlingUnit>
                <cbc:ID>{{ $document->license_plate }}</cbc:ID>
                <cac:TransportEquipment>
                    <cbc:ID>{{ $secondary_license_plates->semitrailer }}</cbc:ID>
                </cac:TransportEquipment>
            </cac:TransportHandlingUnit>
            @endif
        @endif
        <cac:OriginAddress>
            <cbc:ID>{{ $document->origin->location_id }}</cbc:ID>
            <cbc:StreetName>{{ $document->origin->address }}</cbc:StreetName>
        </cac:OriginAddress>
        @if($document->port_code)
        <cac:FirstArrivalPortLocation>
            <cbc:ID>{{ $document->port_code }}</cbc:ID>
        </cac:FirstArrivalPortLocation>
        @endif
    </cac:Shipment>
    @foreach($document->items as $row)
    <cac:DespatchLine>
        <cbc:ID>{{ $loop->iteration }}</cbc:ID>
        <cbc:DeliveredQuantity unitCode="{{ $row->item->unit_type_id }}">{{ $row->quantity }}</cbc:DeliveredQuantity>
        <cac:OrderLineReference>
            <cbc:LineID>{{ $loop->iteration }}</cbc:LineID>
        </cac:OrderLineReference>
        <cac:Item>
            <cbc:Name><![CDATA[{{ $row->item->description }}]]></cbc:Name>
            <cac:SellersItemIdentification>
                <cbc:ID>{{ $row->item->internal_id }}</cbc:ID>
            </cac:SellersItemIdentification>
            @if($row->item->item_code)
            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID="UNSPSC"
                                            listAgencyName="GS1 US"
                                            listName="Item Classification">{{ $row->item->item_code }}</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
            @endif
        </cac:Item>
    </cac:DespatchLine>
    @endforeach
</DespatchAdvice>