<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Expense\Models\ExpenseMethodType;
use Modules\Expense\Http\Resources\ExpenseMethodTypeCollection;
use Exception;

class ExpenseMethodTypeController extends Controller
{

    public function records(Request $request)
    {
        $records = ExpenseMethodType::get();

        return new ExpenseMethodTypeCollection($records);
    }


    public function record($id)
    {
        $record = ExpenseMethodType::findOrFail($id);

        return $record;
    }

    public function store(Request $request)
    {

        $request->validate(['description' => 'required']);
        $id = $request->input('id');
        $record = ExpenseMethodType::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();


        return [
            'success' => true,
            'message' => ($id)?'Método de gasto editado con éxito':'Método de gasto registrado con éxito',
        ];

    }

    public function destroy($id)
    {
        try {

            $record = ExpenseMethodType::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Método de gasto eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => "El Método de gasto esta siendo usado por otros registros, no puede eliminar"] : ['success' => false,'message' => "Error inesperado, no se pudo eliminar el Método de gasto "];

        }

    }




}
