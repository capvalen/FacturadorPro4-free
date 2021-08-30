<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\IncomeType;
use Modules\Finance\Http\Resources\IncomeTypeCollection;
use Exception;

class IncomeTypeController extends Controller
{

    public function records(Request $request)
    {
        $records = IncomeType::get();

        return new IncomeTypeCollection($records);
    }


    public function record($id)
    {
        $record = IncomeType::findOrFail($id);

        return $record;
    }

    public function store(Request $request)
    {

        $request->validate(['description' => 'required']);
        $id = $request->input('id');
        $record = IncomeType::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();


        return [
            'success' => true,
            'message' => ($id)?'Tipo comprobante editado con éxito':'Tipo comprobante registrado con éxito',
        ];

    }

    public function destroy($id)
    {
        try {

            $record = IncomeType::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Tipo comprobante eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => "El Tipo comprobante esta siendo usada por otros registros, no puede eliminar"] : ['success' => false,'message' => "Error inesperado, no se pudo eliminar el Tipo comprobante "];

        }

    }




}
