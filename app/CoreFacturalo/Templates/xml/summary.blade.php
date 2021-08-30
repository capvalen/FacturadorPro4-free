{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<SummaryDocuments
        xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1"
        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
        xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.1</cbc:CustomizationID>
    <cbc:ID>{{ $document->identifier }}</cbc:ID>
    <cbc:ReferenceDate>{{ $document->date_of_reference->format('Y-m-d') }}</cbc:ReferenceDate>
    <cbc:IssueDate>{{ $document->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
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
    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID>{{ $company->number }}</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $company->name }}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    @foreach($document->documents as $row)
    @php($doc = $row->document)
    <sac:SummaryDocumentsLine>
        <cbc:LineID>{{ $loop->iteration }}</cbc:LineID>
        <cbc:DocumentTypeCode>{{ $doc->document_type_id }}</cbc:DocumentTypeCode>
        <cbc:ID>{{ $doc->series }}-{{ $doc->number }}</cbc:ID>
        <cac:AccountingCustomerParty>
            <cbc:CustomerAssignedAccountID>{{ $doc->customer->number }}</cbc:CustomerAssignedAccountID>
            <cbc:AdditionalAccountID>{{ $doc->customer->identity_document_type_id }}</cbc:AdditionalAccountID>
        </cac:AccountingCustomerParty>
        @if(in_array($doc->document_type_id, ['07', '08']))
        @php($affected_document = ($doc->note->affected_document) ? $doc->note->affected_document : $doc->note->data_affected_document)
        <cac:BillingReference>
            <cac:InvoiceDocumentReference>
                <cbc:ID>{{ $affected_document->series }}-{{ $affected_document->number }}</cbc:ID>
                <cbc:DocumentTypeCode>{{ $affected_document->document_type_id }}</cbc:DocumentTypeCode>
            </cac:InvoiceDocumentReference>
        </cac:BillingReference>
        @endif
        @if($doc->perception)
        @php($perception = $doc->perception)
        <sac:SUNATPerceptionSummaryDocumentReference>
            <sac:SUNATPerceptionSystemCode>{{ $perception->code }}</sac:SUNATPerceptionSystemCode>
            <sac:SUNATPerceptionPercent>{{ $perception->percentage }}</sac:SUNATPerceptionPercent>
            <cbc:TotalInvoiceAmount currencyID="PEN">{{ $perception->amount }}</cbc:TotalInvoiceAmount>
            <sac:SUNATTotalCashed currencyID="PEN">{{ round((float)$perception->base + (float)$perception->amount, 2) }}</sac:SUNATTotalCashed>
            <cbc:TaxableAmount currencyID="PEN">{{ $perception->base }}</cbc:TaxableAmount>
        </sac:SUNATPerceptionSummaryDocumentReference>
        @endif
        <cac:Status>
            <cbc:ConditionCode>{{ $document->summary_status_type_id }}</cbc:ConditionCode>
        </cac:Status>
        <sac:TotalAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total }}</sac:TotalAmount>
        @if($doc->total_taxed > 0)
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_taxed }}</cbc:PaidAmount>
            <cbc:InstructionID>01</cbc:InstructionID>
        </sac:BillingPayment>
        @endif
        @if($doc->total_exonerated > 0)
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_exonerated }}</cbc:PaidAmount>
            <cbc:InstructionID>02</cbc:InstructionID>
        </sac:BillingPayment>
        @endif
        @if($doc->total_unaffected > 0)
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_unaffected }}</cbc:PaidAmount>
            <cbc:InstructionID>03</cbc:InstructionID>
        </sac:BillingPayment>
        @endif
        @if($doc->total_exportation > 0)
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_exportation }}</cbc:PaidAmount>
            <cbc:InstructionID>04</cbc:InstructionID>
        </sac:BillingPayment>
        @endif
        @if($doc->total_free > 0)
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_free }}</cbc:PaidAmount>
            <cbc:InstructionID>05</cbc:InstructionID>
        </sac:BillingPayment>
        @endif
        @if($doc->total_charges > 0)
        <cac:AllowanceCharge>
            <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
            <cbc:Amount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_charges }}</cbc:Amount>
        </cac:AllowanceCharge>
        @endif
        @if($doc->total_ivap > 0)
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_ivap }}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_ivap }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>1016</cbc:ID>
                        <cbc:Name>IVAP</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        @endif
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_igv }}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_igv }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        @if($doc->total_isc > 0)
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_isc }}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{$doc->total_isc }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Name>ISC</cbc:Name>
                        <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        @endif
        @if($doc->total_other_taxes > 0)
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_other_taxes }}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_other_taxes }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9999</cbc:ID>
                        <cbc:Name>OTROS</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        @endif
        @if($doc->total_plastic_bag_taxes > 0)
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_plastic_bag_taxes }}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="{{ $doc->currency_type_id }}">{{ $doc->total_plastic_bag_taxes }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>7152</cbc:ID>
                        <cbc:Name>ICBPER</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        @endif
    </sac:SummaryDocumentsLine>
    @endforeach
</SummaryDocuments>