<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SeriesRequest;
use App\Http\Resources\Tenant\SeriesCollection;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Series;

class SeriesController extends Controller
{
    public function create()
    {
        return view('tenant.series.form');
    }

    public function records($establishmentId)
    {
        $records = Series::where('establishment_id', $establishmentId)->get();

        return new SeriesCollection($records);
    }

    public function tables()
    {
        $document_types = DocumentType::whereActive()->whereIn('id', ['01', '03', '07', '08', '09', '20','40', '80'])->get();

        return compact('document_types');
    }

    public function store(SeriesRequest $request)
    {
        $record = Series::where([['document_type_id',$request->document_type_id],['number', $request->number]])->first();

        if($record){
            return [
                'success' => false,
                'message' => 'La serie ya ha sido registrada'
            ];
        }

        $id = $request->input('id');
        $series = Series::firstOrNew(['id' => $id]);
        $series->fill($request->all());
        $series->save();

        return [
            'success' => true,
            'message' => ($id)?'Serie editada con éxito':'Serie registrada con éxito'
        ];
    }

    public function destroy($id)
    {
        $item = Series::findOrFail($id);
        $item->delete();

        return [
            'success' => true,
            'message' => 'Serie eliminada con éxito'
        ];
    }
}
