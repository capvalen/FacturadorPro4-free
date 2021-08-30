<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Models\ExpenseReason;
use Modules\Expense\Http\Resources\ExpenseReasonCollection;
use Exception;

class ExpenseReasonController extends Controller
{

    public function records(Request $request)
    {
        $records = ExpenseReason::get();

        return new ExpenseReasonCollection($records);
    }


    public function record($id)
    {
        $record = ExpenseReason::findOrFail($id);

        return $record;
    }

    public function store(Request $request)
    {

        $request->validate(['description' => 'required']);
        $id = $request->input('id');
        $record = ExpenseReason::firstOrNew(['id' => $id]);
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

            $record = ExpenseReason::findOrFail($id);
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
