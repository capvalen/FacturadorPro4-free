<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\IncomeReason;
use Modules\Finance\Http\Resources\IncomeReasonCollection;
use Exception;

class IncomeReasonController extends Controller
{

    public function records(Request $request)
    {
        $records = IncomeReason::get();

        return new IncomeReasonCollection($records);
    }


    public function record($id)
    {
        $record = IncomeReason::findOrFail($id);

        return $record;
    }

    public function store(Request $request)
    {

        $request->validate(['description' => 'required']);
        $id = $request->input('id');
        $record = IncomeReason::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();


        return [
            'success' => true,
            'message' => ($id)?'Motivo editado con éxito':'Motivo registrado con éxito',
        ];

    }

    public function destroy($id)
    {
        try {

            $record = IncomeReason::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Motivo eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => "El Motivo esta siendo usado por otros registros, no puede eliminar"] : ['success' => false,'message' => "Error inesperado, no se pudo eliminar el Motivo "];

        }

    }




}
