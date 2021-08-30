@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);
    $document_type_dispatcher = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->dispatcher->identity_document_type_id);


    $background = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'blank'.DIRECTORY_SEPARATOR.'hidrochemical.png');


@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>

{{-- <div class="" style="position: absolute; z-index: 0; width:100%; top: 0; left: 0;">
    <img src="data:{{mime_content_type($background)}};base64, {{base64_encode(file_get_contents($background))}}" alt="anulado" class="" style="width: 100%; opacity: 0.3">
</div> --}}
<table class="full-width p-0 mt-20">
    @foreach($document->items as $row)
    <tr>
        <td width="13%" class="align-top">
            {{ $row->item->internal_id }}
        </td>
        <td width="55%" class="align-top">
            {{ $row->item->description }}
        </td>
        <td width="9%" class="align-top">
            @if(((int)$row->quantity != $row->quantity))
                {{ $row->quantity }}
            @else
                {{ number_format($row->quantity, 0) }}
            @endif
        </td>
        <td width="9%" class="align-top">
            {{ $row->item->unit_type_id }}
        </td>
        <td width="12%" class="align-top">
        </td>
    </tr>
    @endforeach
</table>
</body>
</html>
