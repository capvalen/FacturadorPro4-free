@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
@endphp
<html>
<head></head>
<body>
<table class="table-main">
    <tr>
        <td>
            <table>
                <tr><td class="cell-center cell-bold">{{ $company->name }}</td></tr>
                <tr><td class="cell-center">{{ $establishment->address }}</td></tr>
                <tr><td class="cell-center">{{ strtoupper($establishment->department->description).'-'.
                                               strtoupper($establishment->province->description).'-'.
                                               strtoupper($establishment->district->description) }}</td></tr>
                @if($establishment->telephone)
                <tr><td class="cell-center">{{ $establishment->telephone }}</td></tr>
                @endif
                <tr><td class="cell-center cell-bold">RUC {{ $company->number }}</td></tr>
                <tr><td class="cell-center cell-bold">{{ $document->document_type->description }}</td></tr>
                <tr><td class="cell-center cell-bold font-large">{{ $document_number }}</td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="border-top border-bottom">
            <table>
                <tr>
                    <td class="cell-bold">F.EMISIÓN</td>
                    <td class="cell-bold cell-top">:</td>
                    <td>{{ $document->date_of_issue->format('d/m/Y') }}</td></tr>
                <tr>
                    <td class="cell-bold">{{ $customer->identity_document_type->description }}</td>
                    <td>: </td>
                    <td>{{ $customer->number }}</td>
                </tr>
                <tr>
                    <td class="cell-bold cell-top">CLIENTE</td>
                    <td class="cell-bold cell-top">:</td>
                    <td>{{ $customer->name }}</td>
                </tr>
                @if ($customer->address && $customer->address !== '' && $customer->address !== '-')
                <tr>
                    <td class="cell-bold cell-top">DIRECCIÓN</td>
                    <td class="cell-bold cell-top">:</td>
                    <td>{{ $customer->address }}<br/>
                        {{ strtoupper($customer->department->description).'-'.
                           strtoupper($customer->province->description).'-'.
                           strtoupper($customer->district->description) }}
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <thead>
                <tr class="border-top">
                    <td class="width-auto cell-center">CANT.</td>
                    <td>DESCRIPCIÓN</td>
                    <td class="width-auto cell-right">P.U.</td>
                    <td class="width-auto cell-right">DSCTO.</td>
                    <td class="width-auto cell-right">TOTAL</td>
                </tr>
                </thead>
                <tbody>
                    @foreach($document->items as $index => $row)
                    <tr>
                        <td class="cell-center">{{ round($row->quantity, 0) }}</td>
                        <td>
                            {{ $row->item->description }}
                            @if($row->item->is_set == 1)
                                <br>
                                @inject('itemSet', 'App\Services\ItemSetService')
                                @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                                    {{$item}}<br>
                                @endforeach
                            @endif
                        </td>
                        <td class="cell-right">{{ number_format($row->unit_price, 2) }}</td>
                        <td class="cell-right">
                             @if($row->discounts)
                                @foreach($row->discounts as $dscto)
                                    <br/><small>{{ $dscto->factor * 100 }}%</small>
                                @endforeach
                             @else
                                 0
                             @endif
                        </td>
                        <td class="cell-right">{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if ($document->total_exonerated > 0)
                    <tr class="border-top">
                        <td class="cell-right cell-bold" colspan="3">EXONERADA</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_exonerated }}</td>
                    </tr>
                    @endif
                    @if ($document->total_unaffected > 0)
                    <tr>
                        <td class="cell-right cell-bold" colspan="3">INAFECTA</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_unaffected }}</td>
                    </tr>
                    @endif
                    @if ($document->total_taxed > 0)
                    <tr>
                        <td class="cell-right cell-bold" colspan="3">GRAVADA</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_taxed }}</td>
                    </tr>
                    @endif
                    @if ($document->total_free > 0)
                    <tr>
                        <td class="cell-right cell-bold" colspan="3">GRATUITA</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_free }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="cell-right cell-bold" colspan="3">IGV</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_igv }}</td>
                    </tr>
                    @if ($document->total_charge > 0)
                    <tr>
                        <td class="cell-right cell-bold" colspan="3">RECARGO(10%)</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total_charge }}</td>
                    </tr>
                    @endif
                      <tr>
                        <td class="cell-right cell-bold" colspan="3">TOTAL</td>
                        <td></td>
                        <td class="cell-right cell-bold">{{ $document->total }}</td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td class="cell-center border-top border-bottom">
                        @foreach($document->legends as $row)
                        <span class="cell-bold" style="text-transform: uppercase;">SON:</span> {{ $row->value }} {{ $document->currency_type->description }}
                        @endforeach
                    </td>
                </tr>
                <tr><td class="cell-center">Representación impresa de la<br/>{{ $document->document_type->description }}</td></tr>
                @if($company->resolution)
                <tr><td class="cell-center">Autorizado mediante Resolución de Intendencia</td></tr>
                <tr><td class="cell-center">No. {{ $company->resolution }}</td></tr>
                @endif
                <tr><td class="cell-center">Para consultar el comprobante ingresar a {{ $company->url_cpe }}</td></tr>
                <tr><td class="cell-center"><span class="cell-bold">Hash:</span> {{ $document->hash }}</td></tr>
                <tr><td class="cell-center"><img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" /></td></tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
