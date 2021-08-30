<?php

namespace Modules\DocumentaryProcedure\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\DocumentaryProcedure\Models\DocumentaryProcess;
use Modules\DocumentaryProcedure\Http\Requests\ProcessRequest;

class DocumentaryProcessController extends Controller
{
	public function index()
	{
		$processes = DocumentaryProcess::orderBy('id', 'DESC');
		if (request()->ajax()) {
			$filter = request('name');
			if ($filter) {
				$processes = $processes->where('name', 'like', "%$filter%")->get();
			}

			return response()->json(['data' => $processes], 200);
		}
		$processes = $processes->get();

		return view('documentaryprocedure::process', compact('processes'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Response
	 */
	public function store(ProcessRequest $request)
	{
		$process = DocumentaryProcess::create($request->only('name', 'description', 'active'));

		return response()->json([
			'data'    => $process,
			'message' => 'Proceso guardada de forma correcta.',
			'succes'  => true,
		], 200);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(ProcessRequest $request, $id)
	{
		$process = DocumentaryProcess::findOrFail($id);
		$process->fill($request->only('name', 'description', 'active'));
		$process->save();

		return response()->json([
			'data'    => $process,
			'message' => 'Proceso actualizada de forma correcta.',
			'succes'  => true,
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
			$process = DocumentaryProcess::findOrFail($id);
			$process->delete();

			return response()->json([
				'data'    => null,
				'message' => 'Proceso eliminada de forma correcta.',
				'succes'  => true,
			], 200);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'data'    => 'Ocurrió un error al procesar su petición. Detalles: ' . $th->getMessage()
			], 500);
		}
	}
}
