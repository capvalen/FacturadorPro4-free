@php
    $establishment = $document->establishment;
@endphp
{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<Perception xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:Perception-1"
            xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
            xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
            xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
            xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
            xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
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
    <cbc:ID>{{ $document->series }}-{{ $document->number }}</cbc:ID>
    <cbc:IssueDate>{{ $document->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
    <cbc:IssueTime>{{ $document->time_of_issue }}</cbc:IssueTime>
    <cac:AgentParty>
        <cac:PartyIdentification>
            <cbc:ID schemeID="6">{{ $company->number }}</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyName>
            <cbc:Name><![CDATA[{{ $company->trade_name }}]]></cbc:Name>
        </cac:PartyName>
        <cac:PostalAddress>
            <cbc:ID>{{ $establishment->district_id }}</cbc:ID>
            <cbc:StreetName><![CDATA[{{ $establishment->address }}]]></cbc:StreetName>
            <cbc:CityName>{{ $establishment->department->description }}</cbc:CityName>
            <cbc:CountrySubentity>{{ $establishment->province->description }}</cbc:CountrySubentity>
            <cbc:District>{{ $establishment->district->description }}</cbc:District>
            <cac:Country>
                <cbc:IdentificationCode>{{ $establishment->country_id }}</cbc:IdentificationCode>
            </cac:Country>
        </cac:PostalAddress>
        <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[{{ $company->name }}]]></cbc:RegistrationName>
        </cac:PartyLegalEntity>
    </cac:AgentParty>
    <cac:ReceiverParty>
        <cac:PartyIdentification>
            <cbc:ID schemeID="{{ $document->customer->identity_document_type_id }}">{{ $document->customer->number }}</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[{{ $document->customer->name }}]]></cbc:RegistrationName>
        </cac:PartyLegalEntity>
    </cac:ReceiverParty>
    <sac:SUNATPerceptionSystemCode>{{ $document->perception_type_id }}</sac:SUNATPerceptionSystemCode>
    <sac:SUNATPerceptionPercent>{{ $document->perception_type->percentage }}</sac:SUNATPerceptionPercent>
    @if($document->observation)
    <cbc:Note><![CDATA[{{ $document->observation }}]]></cbc:Note>
    @endif
    <cbc:TotalInvoiceAmount currencyID="PEN">{{ $document->total_perception }}</cbc:TotalInvoiceAmount>
    <sac:SUNATTotalCashed currencyID="PEN">{{ $document->total }}</sac:SUNATTotalCashed>
    @foreach($document->documents as $doc)
    <sac:SUNATPerceptionDocumentReference>
        <cbc:ID schemeID="{{ $doc->document_type_id }}">{{ $doc->series }}-{{ $doc->number }}</cbc:ID>
        <cbc:IssueDate>{{ $doc->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
        <cbc:TotalInvoiceAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_document }}</cbc:TotalInvoiceAmount>
        @if($doc->payments)
        @foreach($doc->payments as $payment)
        <cac:Payment>
            <cbc:ID>{{ $loop->iteration }}</cbc:ID>
            <cbc:PaidAmount currencyID="{{ $payment->currency_type_id }}">{{ $payment->total_payment }}</cbc:PaidAmount>
            <cbc:PaidDate>{{ $payment->date_of_payment }}</cbc:PaidDate>
        </cac:Payment>
        @endforeach
        @endif
        @if($doc->total_perception && $doc->total_payment && $doc->date_of_perception)
        <sac:SUNATPerceptionInformation>
            <sac:SUNATPerceptionAmount currencyID="PEN">{{ $doc->total_perception  }}</sac:SUNATPerceptionAmount>
            <sac:SUNATPerceptionDate>{{ $doc->date_of_perception->format('Y-m-d') }}</sac:SUNATPerceptionDate>
            <sac:SUNATNetTotalCashed currencyID="PEN">{{ $doc->total_payment }}</sac:SUNATNetTotalCashed>
            @if($doc->exchange_rate)
            <cac:ExchangeRate>
                <cbc:SourceCurrencyCode>{{ $doc->exchange_rate->currency_type_id_source }}</cbc:SourceCurrencyCode>
                <cbc:TargetCurrencyCode>{{ $doc->exchange_rate->currency_type_id_target }}</cbc:TargetCurrencyCode>
                <cbc:CalculationRate>{{ $doc->exchange_rate->factor }}</cbc:CalculationRate>
                <cbc:Date>{{ $doc->exchange_rate->date_of_exchange_rate }}</cbc:Date>
            </cac:ExchangeRate>
            @endif
        </sac:SUNATPerceptionInformation>
        @endif
    </sac:SUNATPerceptionDocumentReference>
    @endforeach
</Perception>