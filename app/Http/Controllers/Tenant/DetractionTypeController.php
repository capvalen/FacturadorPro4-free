<?php
namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Catalogs\DetractionType;
use App\Models\Tenant\Catalogs\OperationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DetractionTypeRequest;
use App\Http\Resources\Tenant\DetractionTypeCollection;
use App\Http\Resources\Tenant\DetractionTypeResource;
use Exception;

class DetractionTypeController extends Controller
{
    public function records()
    {
        $records = DetractionType::all();

        return new DetractionTypeCollection($records);
    }

    public function record($id)
    {
        $record = new DetractionTypeResource(DetractionType::findOrFail($id));

        return $record;
    }

    public function tables()
    {
        $operation_types = OperationType::whereActive()->whereIn('id',['1001','1002','1003','1004'])->get();

        return compact('operation_types');
    }

    public function store(DetractionTypeRequest $request)
    {
        $id = $request->input('id');
        $record = DetractionType::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Tipo de detracción editada con éxito':'Tipo de detracción registrada con éxito'
        ];
    }

    public function destroy($id)
    {
        try {
            
            $record = DetractionType::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Tipo de detracción eliminada con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El Tipo de detracción esta siendo usada por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el Tipo de detracción'];

        }

        
    }
}