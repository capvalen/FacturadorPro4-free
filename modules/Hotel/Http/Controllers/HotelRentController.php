<?php

namespace Modules\Hotel\Http\Controllers;

use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRoom;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Establishment;
use Modules\Hotel\Models\HotelRentItem;
use App\Models\Tenant\PaymentMethodType;
use Modules\Finance\Traits\FinanceTrait;
use App\Models\Tenant\Catalogs\DocumentType;
use Modules\Hotel\Http\Requests\HotelRentRequest;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use Modules\Hotel\Http\Requests\HotelRentItemRequest;

class HotelRentController extends Controller
{
    use FinanceTrait;

	public function rent($roomId)
	{
		$room = HotelRoom::with('category', 'rates.rate')
			->findOrFail($roomId);

		$affectation_igv_types = AffectationIgvType::whereActive()->get();

		return view('hotel::rooms.rent', compact('room', 'affectation_igv_types'));
	}

	public function store(HotelRentRequest $request, $roomId)
	{
		DB::connection('tenant')->beginTransaction();
		try {
			$room = HotelRoom::findOrFail($roomId);
			if ($room->status !== 'DISPONIBLE') {
				return response()->json([
					'success' => true,
					'message' => 'La habitación seleccionada no esta disponible',
				], 500);
			}

			$request->merge(['hotel_room_id' => $roomId]);
			$now = now();
			$request->merge(['input_date' => $now->format('Y-m-d')]);
			$request->merge(['input_time' => $now->format('H:i:s')]);
			$rent = HotelRent::create($request->only('customer_id', 'customer', 'notes', 'towels', 'hotel_room_id', 'duration', 'quantity_persons', 'payment_status', 'output_date', 'output_time', 'input_date', 'input_time'));

			$room->status = 'OCUPADO';
			$room->save();

			// Agregando la habitación a la lista de productos
			$item = new HotelRentItem();
			$item->type = 'HAB';
			$item->hotel_rent_id = $rent->id;
			$item->item_id = $request->product['item_id'];
			$item->item = $request->product;
			$item->payment_status = $request->payment_status;
			$item->save();

			DB::connection('tenant')->commit();

			return response()->json([
				'success' => true,
				'message' => 'Habitación rentada de forma correcta.',
			], 200);
		} catch (\Throwable $th) {
			DB::connection('tenant')->rollBack();

			return response()->json([
				'success' => true,
				'message' => 'No se puede procesar su transacción. Detalles: ' . $th->getMessage(),
			], 500);
		}
	}

	public function searchCustomers()
	{
		$customers = $this->customers();

		return response()->json([
			'customers' => $customers,
		], 200);
	}

	public function showFormAddProduct($rentId)
	{
		$rent = HotelRent::with('room')
			->findOrFail($rentId);

		$configuration = Configuration::first();

		$products = HotelRentItem::where('hotel_rent_id', $rentId)
			->where('type', 'PRO')
			->get();

		return view('hotel::rooms.add-product-to-room', compact('rent', 'configuration', 'products'));
	}

	public function addProductsToRoom(HotelRentItemRequest $request, $rentId)
	{
		foreach ($request->products as $product) {
			$item = HotelRentItem::where('hotel_rent_id', $rentId)
				->where('item_id', $product['item_id'])
				->first();
			if (!$item) {
				$item = new HotelRentItem();
				$item->type = 'PRO';
				$item->hotel_rent_id = $rentId;
				$item->item_id = $product['item_id'];
			}
			$item->item = $product;
			$item->payment_status = $product['payment_status'];
			$item->save();
		}

		return response()->json([
			'success' => true,
			'message' => 'Información actualizada.'
		], 200);
	}

	public function showFormChekout($rentId)
	{
		$rent = HotelRent::with('room', 'room.category', 'items')
			->findOrFail($rentId);

		$token = auth()->user()->api_token;

		$room = $rent->items->firstWhere('type', 'HAB');

		$customer = Person::withOut('department', 'province', 'district')
			->findOrFail($rent->customer_id);

        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $series = Series::where('establishment_id', $establishment->id)->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
        $user = auth()->user();
        $warehouse_id = $user->establishment_id;

		return view('hotel::rooms.checkout', compact('rent', 'room', 'token', 'customer', 'payment_method_types', 'payment_destinations', 'series', 'document_types_invoice', 'warehouse_id'));
	}

	public function finalizeRent($rentId)
	{
		$rent = HotelRent::findOrFail($rentId);
		$rent->update([
			'arrears' => request('arrears'),
			'status'  => 'FINALIZADO'
		]);
		HotelRoom::where('id', $rent->hotel_room_id)
			->update([
				'status' => 'LIMPIEZA'
			]);

		return response()->json([
			'success' => true,
			'message' => 'Información procesada de forma correcta.'
		], 200);
	}

	private function customers()
	{
		$customers = Person::with('addresses')
			->whereType('customers')
			->whereIsEnabled()
			->whereIn('identity_document_type_id', [1, 6])
			->orderBy('name');

		$query = request('input');
		if ($query) {
			if (is_numeric($query)) {
				$customers = $customers->where('number', 'like', "%{$query}%");
			} else {
				$customers = $customers->where('name', 'like', "%{$query}%");
			}
		}

		$customers = $customers->take(20)
			->get()
			->transform(function ($row) {
				return [
					'id'                          => $row->id,
					'description'                 => $row->number . ' - ' . $row->name,
					'name'                        => $row->name,
					'number'                      => $row->number,
					'identity_document_type_id'   => $row->identity_document_type_id,
					'identity_document_type_code' => $row->identity_document_type->code,
					'addresses'                   => $row->addresses,
					'address'                     => $row->address,
					'internal_code'               => $row->internal_code
				];
			});

		return $customers;
	}

	public function tables()
	{
		$customers = $this->customers();

		return response()->json([
			'customers'            => $customers,
		], 200);
	}
}
