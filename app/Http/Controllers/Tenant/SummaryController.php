<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CoreFacturalo\Facturalo;
use App\Http\Requests\Tenant\{
    SummaryDocumentsRequest,
    SummaryRequest
};
use App\Http\Resources\Tenant\{
    DocumentCollection,
    SummaryResource,
    SummaryCollection
};
use App\Traits\SummaryTrait;
use App\Models\Tenant\{
    Document,
    Summary,
    Company
};
use Exception;

class SummaryController extends Controller
{
    use StorageDocument, SummaryTrait;
    
    public function __construct() {
        $this->middleware('input.request:summary,web', ['only' => ['store']]);
    }
    
    public function index() {
        return view('tenant.summaries.index');
    }
    
    public function records(Request $request) {
        
        $records = Summary::where([ ['summary_status_type_id','1'], [ $request->column, 'like', "%{$request->value}%" ]])
            ->latest();
         
        return new SummaryCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function columns()
    {
        return [
            'date_of_issue' => 'Fecha de emisión'
        ];
    }
    
    public function documents(SummaryDocumentsRequest $request) {
        $company = Company::active();
        $date_of_reference = $request->input('date_of_reference');
        
        $documents = Document::query()
            ->where('date_of_issue', $request->input('date_of_reference'))
            ->where('soap_type_id', $company->soap_type_id)
            ->where('group_id', '02')
            ->where('state_type_id', '01')
            ->take(500)
            ->get();
            
        if (count($documents) === 0) {
            return [
                'success' => false,
                'message' => "No se encontraron documentos con la fecha {$date_of_reference}",
            ];
        }

        return [
            'success' => true,
            'data' => new DocumentCollection($documents)
        ];
        
    }
    
    public function store(SummaryRequest $request) {
        return $this->save($request);
    }
    
    public function status($summary_id) {
        return $this->query($summary_id);
    }

    public function destroy($voided_id)
    {
        $summary = Summary::find($voided_id);
        foreach ($summary->documents as $doc)
        {
            $doc->document->update([
                'state_type_id' => ($summary->summary_status_type_id === '1')?'01':'05'
            ]);
        }
        $summary->delete();

        return [
            'success' => true,
            'message' => 'Resumen eliminada con éxito'
        ];
    }
    
    public function record($id)
    {
        $record = new SummaryResource(Summary::findOrFail($id));

        return $record;
    }

}
