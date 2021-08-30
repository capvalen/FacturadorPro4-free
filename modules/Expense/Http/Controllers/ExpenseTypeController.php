<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Models\ExpenseType;
use Modules\Expense\Http\Resources\ExpenseTypeCollection;
use Exception;

class ExpenseTypeController extends Controller
{

    public function records(Request $request)
    {
        $records = ExpenseType::get();

        return new ExpenseTypeCollection($records);
    }


    public function record($id)
    {
        $record = ExpenseType::findOrFail($id);

        return $record;
    }

    public function store(Request $request)
    {

        $request->validate(['description' => 'required']);
        $id = $request->input('id');
        $record = ExpenseType::firstOrNew(['id' => $id]);
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

            $record = ExpenseType::findOrFail($id);
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
