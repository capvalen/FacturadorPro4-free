<?php
namespace Modules\Purchase\Http\Controllers;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\UnitType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Requests\Tenant\ItemRequest;
use App\Models\Tenant\User;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\ItemUnitType;
use Exception;
use Illuminate\Http\Request;
use Modules\Purchase\Http\Requests\FixedAssetItemRequest;
use Modules\Purchase\Models\FixedAssetItem;
use Modules\Purchase\Http\Resources\FixedAssetItemCollection;
use Modules\Purchase\Http\Resources\FixedAssetItemResource;

class FixedAssetItemController extends Controller
{
    public function index()
    {
        return view('purchase::fixed_asset_items.index');
    }
  
    public function columns()
    {
        return [
            'name' => 'Nombre',
            'internal_id' => 'Código interno', 
        ];
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new FixedAssetItemCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function getRecords($request){

        $records = FixedAssetItem::whereTypeUser()->where($request->column, 'like', "%{$request->value}%");

        return $records->orderBy('description');

    }

    public function create()
    {
        return view('tenant.items.form');
    }

    public function tables()
    {
        $unit_types = UnitType::whereActive()->orderByDescription()->get();
        $currency_types = CurrencyType::whereActive()->orderByDescription()->get();
        $affectation_igv_types = AffectationIgvType::whereActive()->get();

        return compact('unit_types', 'currency_types', 'affectation_igv_types');
    }

    public function record($id)
    {
        $record = new FixedAssetItemResource(FixedAssetItem::findOrFail($id));

        return $record;
    }

    public function store(FixedAssetItemRequest $request) {

        $id = $request->input('id');
        $item = FixedAssetItem::firstOrNew(['id' => $id]);
        $item->item_type_id = '01';
        $item->fill($request->all());
        $item->save();

        return [
            'success' => true,
            'message' => ($id)?'Ítem editado con éxito':'Ítem registrado con éxito',
            'id' => $item->id
        ];
    }

    public function destroy($id)
    {
        try {

            $item = FixedAssetItem::findOrFail($id);
            $item->delete();

            return [
                'success' => true,
                'message' => 'Ítem eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El ítem esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el ítem'];

        }


    } 
}
