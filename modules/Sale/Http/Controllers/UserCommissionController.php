<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Sale\Http\Resources\UserCommissionCollection;
use Modules\Sale\Http\Resources\UserCommissionResource;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Http\Requests\UserCommissionRequest;
use Exception;
use Modules\Sale\Models\UserCommission;
use App\Models\Tenant\User;

class UserCommissionController extends Controller
{

    public function index()
    {
        return view('sale::user-commissions.index');
    }

 
    public function columns()
    {
        return [
            'id' => 'Número',
        ];
    }
 

    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new UserCommissionCollection($records->paginate(config('tenant.items_per_page')));
    }

    private function getRecords($request){

        if($request->column == 'customer'){
            
            $records = UserCommission::whereHas('person', function($query) use($request){
                            $query->where('name', 'like', "%{$request->value}%");
                        });

        }else{

            $records = UserCommission::where($request->column, 'like', "%{$request->value}%");
        
        }
        
        return $records->whereTypeUser()->latest();
    } 


    public function tables() {

        $users = User::get(['id', 'name']);

        return compact('users');

    }

    public function record($id)
    {
        $record = new UserCommissionResource(UserCommission::findOrFail($id));

        return $record;
    }
 

    public function store(UserCommissionRequest $request) {

        $id = $request->input('id');
        $user_commission = UserCommission::firstOrNew(['id' => $id]);
        $user_commission->fill($request->all());
        $user_commission->save();

        return [
            'success' => true,
            'message' => ($id)?'Comisión editada con éxito':'Comisión registrada con éxito'
        ];

    }
 
  

    public function destroy($id)
    {

        $record = UserCommission::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Comisión eliminada con éxito'
        ];

    }

}
