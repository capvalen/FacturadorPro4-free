<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Document;
use App\Models\Tenant\Series;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\OfflineTrait;

class ReportConsistencyDocumentController extends Controller
{

    use OfflineTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('tenant.reports.consistency-documents.index');
    }

    /**
     * Lists
     * @return \Illuminate\Http\Response
     */
    public function lists(Request $request) {
        $dates = [
            'min' =>  Carbon::parse(Document::select('created_at')->min('created_at')),
            'max' =>  Carbon::parse(Document::select('created_at')->max('created_at')),
        ];
        $dates['start_date'] = ($dates['max']->addMonth(-1) < $dates['max'])?$dates['max']->addMonth(-1):$dates['max'];
        $dates['end_date'] = $dates['max'];
        if ($request->has('date_start') && !empty($request->date_start) && is_array($request->date_start)) {
            $start_date = Carbon::parse($request->date_start[0]);
            $end_date = Carbon::parse($request->date_start[1]);
            /*
             // Se remueve la validacion de  getIsClient por la aplicacion del issue #425
            if (!$this->getIsClient()) {
                $dates['start_date'] = ($start_date < Carbon::now()->startOfMonth()) ? $dates['start_date'] : $start_date;
                $dates['end_date'] = ($end_date > Carbon::now()) ? $dates['end_date'] : $end_date;
            } else {
                $dates['start_date'] = $start_date;
                $dates['end_date'] = $end_date;
            }
            */
            $dates['start_date'] = $start_date;
            $dates['end_date'] = $end_date;
        }

        return Series::query()
            ->select('number')
            ->with(['documents' => function ($queryDocuments) use ($dates) {
                $queryDocuments->whereBetween('created_at', [
                        $dates['start_date'],
                        $dates['end_date']
                    ]);

                $queryDocuments->select('series', 'number', 'state_type_id');
            }])
            ->get()
            ->map(function ($serie) use ($dates) {
                $start = $serie->documents->min('number') ?? 0;
                $end = $serie->documents->max('number') ?? 0;
                $numbers = (count($serie->documents) > 0) ? $serie->documents->pluck('number')->toArray() : [0];
                $registered = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '01')->pluck('number')->toArray() : [];
                $sent = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '03')->pluck('number')->toArray() : [];
                $accepted = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '05')->pluck('number')->toArray() : [];
                $observed = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '07')->pluck('number')->toArray() : [];
                $rejected = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '09')->pluck('number')->toArray() : [];
                $canceled = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '11')->pluck('number')->toArray() : [];
                $byVoiding = (count($serie->documents) > 0) ? $serie->documents()->where('state_type_id', '13')->pluck('number')->toArray() : [];

                return [
                    'serie' => $serie,
                    'start' => $start,
                    'end' => $end,
                    'diff' => join(', ', array_diff(range($start, $end), $numbers)),
                    'registered' => join(', ', $registered),
                    'sent' => join(', ', $sent),
                    'accepted' => join(', ', $accepted),
                    'observed' => join(', ', $observed),
                    'rejected' => join(', ', $rejected),
                    'canceled' => join(', ', $canceled),
                    'byVoiding' => join(', ', $byVoiding),
                    'min' => $dates['min']->toIsoString(),
                    'max' => $dates['max']->toIsoString(),
                ];
            });
    }
}
