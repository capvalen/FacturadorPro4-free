<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Hotel\Http\Requests\HotelAddRateToRoomRequest;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelFloor;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Http\Requests\HotelRoomRequest;
use Modules\Hotel\Http\Requests\HotelFloorRequest;
use Modules\Hotel\Models\HotelRate;
use Modules\Hotel\Models\HotelRoomRate;

class HotelRoomController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
		$rooms = HotelRoom::with('category', 'floor')
			->orderBy('id', 'DESC');

		if (request()->ajax()) {
			if (request('hotel_floor_id')) {
				$rooms = $rooms->where('hotel_floor_id', request('hotel_floor_id'));
			}
			if (request('hotel_category_id')) {
				$rooms = $rooms->where('hotel_category_id', request('hotel_category_id'));
			}
			if (request('status')) {
				$rooms = $rooms->where('status', request('status'));
			}
			if (request('name')) {
				$rooms = $rooms->where('name', 'like', '%' . request('name') . '%');
			}

			return response()->json([
				'success' => true,
				'rooms'   => $rooms->paginate(25),
			], 200);
		}

		$rooms = $rooms->paginate(25);

		$categories = HotelCategory::where('active', true)
			->orderBy('description')
			->get();

		$floors = HotelFloor::where('active', true)
			->orderBy('description')
			->get();

		$roomStatus = HotelRoom::$status;

		return view('hotel::rooms.index', compact('rooms', 'floors', 'categories', 'roomStatus'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(HotelRoomRequest $request)
	{
		$room = HotelRoom::create($request->only('description', 'active', 'name', 'hotel_category_id', 'hotel_floor_id', 'item_id'));
		$room->status = 'DISPONIBLE';
		$room->load('category', 'floor');

		return response()->json([
			'success' => true,
			'data'    => $room
		], 200);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(HotelFloorRequest $request, $id)
	{
		$room = HotelRoom::findOrFail($id);
		$room = $room->fill($request->only('description', 'active', 'name', 'hotel_category_id', 'hotel_floor_id'));
		$room->save();

		$room->load('category', 'floor');

		return response()->json([
			'success' => true,
			'data'    => $room
		], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			HotelRoom::where('id', $id)
				->delete();

			return response()->json([
				'success' => true,
				'message' => 'Información actualizada'
			], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}

	public function changeRoomStatus($roomId)
	{
		HotelRoom::where('id', $roomId)
			->update([
				'status' => request('status')
			]);

		return response()->json([
			'success' => true,
			'message' => 'La habitación cambió su estado a DISPONIBLE',
		], 200);
	}

	public function tables()
	{
		$rates = HotelRate::where('active', true)
			->orderBy('description')
			->get();

		return response()->json([
			'success' => true,
			'rates'   => $rates,
		], 200);
	}

	public function myRates($roomId)
	{
		$myRates = HotelRoomRate::with('rate')
			->where('hotel_room_id', $roomId)
			->get();

		return response()->json([
			'success'      => true,
			'room_rates'   => $myRates,
		], 200);
	}

	public function addRateToRoom(HotelAddRateToRoomRequest $request, $roomId)
	{
		$roomRate = HotelRoomRate::create($request->only('hotel_room_id', 'hotel_rate_id', 'price'));
		$roomRate->load('rate');

		return response()->json([
			'success'     => true,
			'room_rate'   => $roomRate,
		], 200);
	}

	public function deleteRoomRate($roomId, $roomRateId)
	{
		HotelRoomRate::where('hotel_room_id', $roomId)
			->where('id', $roomRateId)
			->delete();

		return response()->json([
			'success'     => true,
			'message'     => 'Información actualizada',
		], 200);
	}
}
