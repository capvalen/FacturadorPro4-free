<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Tenant\Series;
use App\Models\Tenant\Catalogs\DocumentType;

class OrderNoteDocumentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $all_series = Series::where('establishment_id', auth()->user()->establishment_id)->get();
        $all_document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();

        return $this->collection->transform(function($row, $key) use($all_series, $all_document_types_invoice){

            $document_types = ($row->customer->identity_document_type_id !== '6') ? $all_document_types_invoice->filter(function($row){ return in_array($row->id, ['03', '80']);}) : $all_document_types_invoice;

            $series = ($row->customer->identity_document_type_id !== '6') ? $all_series->filter(function($row){ return ($row->document_type_id == '03') ;}) : $all_series->filter(function($row){ return ($row->document_type_id == '01');});

            return [
                'id' => null,
                'index_id' => $row->id,
                'establishment_id' => $row->establishment_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'identifier' => $row->identifier,
                'customer_name' => $row->customer->name,
                'customer_number' => $row->customer->number,
                'total' => number_format($row->total,2),
                'selected' => false,
                'document_type_id' => ($row->customer->identity_document_type_id !== '6') ? '03' : '01',
                'series_id' => count($series) > 0 ? $series->first()->id : null,
                'series' => $series,
                'document_types' => $document_types,
                'order_note' => $row,
            ];
        });
    }

}
