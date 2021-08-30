<?php
namespace Modules\Order\Http\Controllers;

use Modules\Order\Http\Requests\DispatcherRequest;
use Modules\Order\Http\Resources\DispatcherCollection;
use Modules\Order\Http\Resources\DispatcherResource;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Http\Controllers\Controller;
use Modules\Order\Models\Dispatcher;
use Illuminate\Http\Request;

class DispatcherController extends Controller
{

    public function index()
    {
        return view('order::dispatchers.index');
    }

    public function columns()
    {
        return [
            'name' => 'Nombre',
            'number' => 'Número',
        ];
    }

    public function records(Request $request)
    {

        $records = Dispatcher::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('name');

        return new DispatcherCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function tables()
    {
        $identity_document_types = IdentityDocumentType::whereActive()->get();
        $api_service_token = config('configuration.api_service_token');

        return compact('identity_document_types', 'api_service_token');
    }

    public function record($id)
    {
        $record = new DispatcherResource(Dispatcher::findOrFail($id));

        return $record;
    }

    public function store(DispatcherRequest $request)
    {

        $id = $request->input('id');
        $record = Dispatcher::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Transportista editado con éxito':'Transportista registrado con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {

        $record = Dispatcher::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Transportista eliminado con éxito'
        ];

    }

}
