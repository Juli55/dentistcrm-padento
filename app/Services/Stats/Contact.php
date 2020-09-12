<?php

namespace App\Services\Stats;

use App\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Contact
{
    protected $request;

    protected $query;

    protected $from;

    protected $to;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->from = $request->has('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->subMonths(5);

        $this->to = $request->has('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now();

        $this->makeQuery();
    }

    protected function makeQuery()
    {
        $query = Patient::query();

        $request = $this->request;

        if ($request->has('lab_ids') && !empty($request->lab_ids)) {
            $query->whereIn('lab_id', $request->lab_ids);
        }

        if ($request->queued || $request->queued === false || $request->queued === 0) {
            $query->whereHas('lab', function ($q) use ($request) {
                if ($request->queued) {
                    $q->queued();
                } else {
                    $q->notQueued();
                }
            });
//            $query->where('queued', $request->queued);
        }

        if (!is_null($request->active_lab)) {
            $active = $request->active_lab;

            $query->whereHas('lab', function ($q) use ($active) {
                if ($active) {
                    $q->active();
                } else {
                    $q->inactive();
                }
            });
        }

        $this->query = $query;
    }

    public function handle()
    {
        return [
            'labels'         => $this->getLabels(),
            'dentistlabels'  => $this->getDentistLabels(),
            'current_range'  => $this->getCurrentRange(),
            'previous_range' => $this->getPreviousRange(),
            'all'            => $this->allTime(),
        ];
    }

    protected function getLabels()
    {
        return collect(contact_phases())->values()->all();
    }


    protected function getDentistLabels()
    {
        return collect(dentist_phases())->values()->all();
    }

    protected function getCurrentRange()
    {
        $query = clone $this->query;

        $query->has('lab')->whereBetween('created_at', [$this->from, $this->to]);

        return $this->getPhaseCount($query);
    }

    protected function getPreviousRange()
    {
        $query = clone $this->query;

        $diffInDays = $this->from->diffInDays($this->to) + 1;

        $from = $this->from->copy()->subDays($diffInDays);
        $to = $this->to->copy()->subDays($diffInDays);

        $query->has('lab')->whereBetween('created_at', [$from, $to]);

        return $this->getPhaseCount($query);
    }

    protected function allTime()
    {
        $query = clone $this->query;

        $from = $this->from->copy();
        $to = $this->to->copy();

        if ($to->gte(Carbon::now())) {
            $to = Carbon::now();
        }

        $result = collect();

        while ($to->gte($from)) {
            $q = clone $query;

            $q->whereMonth('created_at', '=', $to->month)
                ->whereYear('created_at', '=', $to->year);

            $patientWithoutLab = clone $q;

            $phases = $this->getPhaseCount($q->has('lab'));
            $phases[] = $patientWithoutLab->doesntHave('lab')->count();

//            $result->put($to->formatLocalized('%B %Y'), $this->getPhaseCount($q));
            $result->put(trans('months.' . $to->format('F')) . ' ' . $to->format('Y'), $phases);

            $to->subMonth();
        }

        return $result->toArray();
    }

    protected function getPhaseCount($query)
    {
        $one = clone $query;
        $two = clone $query;
        $three = clone $query;
        $four = clone $query;
        $five = clone $query;
        $six = clone $query;

        return [
            $one->where('phase', 1)->count(),
            $two->where('phase', 2)->count(),
            $three->where('phase', 3)->count(),
            $four->where('phase', 4)->count(),
            $five->where('phase', 5)->count(),
            $six->where('phase', 6)->count(),
        ];
    }
}