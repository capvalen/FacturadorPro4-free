<?php
namespace Modules\Order\Http\Controllers;

use Modules\Order\Http\Requests\DriverRequest;
use Modules\Order\Http\Resources\DriverCollection;
use Modules\Order\Http\Resources\DriverResource;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Http\Controllers\Controller;
use Modules\Order\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{

    public function index()
    {
        return view('order::drivers.index');
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

        $records = Driver::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('name');

        return new DriverCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function tables()
    {
        $identity_document_types = IdentityDocumentType::whereActive()->get();
        $api_service_token = config('configuration.api_service_token');

        return compact('identity_document_types', 'api_service_token');
    }

    public function record($id)
    {
        $record = new DriverResource(Driver::findOrFail($id));

        return $record;
    }

    public function store(DriverRequest $request)
    {

        $id = $request->input('id');
        $record = Driver::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Conductor editado con éxito':'Conductor registrado con éxito',
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {

        $record = Driver::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Conductor eliminado con éxito'
        ];

    }

}
