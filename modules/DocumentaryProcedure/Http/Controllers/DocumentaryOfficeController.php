<?php

namespace Modules\DocumentaryProcedure\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\DocumentaryProcedure\Http\Requests\OfficeRequest;
use Modules\DocumentaryProcedure\Models\DocumentaryOffice;

class DocumentaryOfficeController extends Controller
{
	public function index()
	{
		$offices = DocumentaryOffice::orderBy('id', 'DESC');
		if (request()->ajax()) {
			$filter = request('name');
			if ($filter) {
				$offices = $offices->where('name', 'like', "%$filter%")->get();
			}

			return response()->json(['data' => $offices], 200);
		}
		$offices = $offices->get();

		return view('documentaryprocedure::offices', compact('offices'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(OfficeRequest $request)
	{
		$office = DocumentaryOffice::create($request->only('name', 'description', 'active'));

		return response()->json([
            'data' => $office,
            'message' => 'Oficina guardada de forma correcta.',
            'succes' => true,
        ], 200);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(OfficeRequest $request, $id)
	{
		$office = DocumentaryOffice::findOrFail($id);
		$office->fill($request->only('name', 'description', 'active'));
		$office->save();

		return response()->json([
            'data' => $office,
            'message' => 'Oficina actualizada de forma correcta.',
            'succes' => true,
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
			$office = DocumentaryOffice::findOrFail($id);
			$office->delete();

			return response()->json([
                'data' => null,
                'message' => 'Oficina eliminada de forma correcta.',
                'succes' => true,
            ], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}
}
