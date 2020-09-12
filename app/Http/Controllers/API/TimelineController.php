<?php

namespace App\Http\Controllers\API;

use App\DentistContact;
use App\Lab;
use App\Patient;
use App\User;
use App\Activity;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimelineController extends Controller
{
    public function index(Request $request)
    {
//        $user = $request->user();
        $user = User::first();

        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->with('causer', 'subject')
            ->latest()->get();

        return view('activities.timeline', compact('activities'));
    }

    public function forContact(Request $request)
    {


//        $patient=Patient::find($request->contact_id);
//        $activities = Activity::where(function ($query) use ($request) {
//            $query->where(function ($q) use ($request) {
//                $q->where('subject_id', $request->contact_id)->where('subject_type', Patient::class);
//            });
//        })->where('description', '<>', 'note_added')
//            ->where('created_at','>',  $patient->created_at->toDateTimeString())
//            ->with('causer', 'subject')->latest()->get();

        if(is_numeric($request->contact_id)) {

            $activities = Activity::where('subject_id', $request->contact_id)
                ->where('subject_type', Patient::class)
                ->where('description', '<>', 'note_added')
                ->with('causer', 'subject')->latest()->get();

            if ($activities) {
                return view('activities.timeline', compact('activities'));
            }
        }
        $activities = collect([]);
        return view('activities.timeline', compact('activities'));
    }

    public function notesForContact(Request $request)

    {
        if(is_numeric($request->contact_id)) {
            $activities = Activity::where('subject_id', $request->contact_id)
                ->where('subject_type', Patient::class)
                ->where('description', 'note_added')
                ->with('causer', 'subject')
                ->latest()->get();

            if ($activities) {
                return view('activities.timeline', compact('activities'));
            }
        }
        $activities = collect([]);
        return view('activities.timeline', compact('activities'));
    }


    public function forDentistContact(Request $request)
    {
        $activities = Activity::where('subject_id', $request->contact_id)
            ->where('subject_type', DentistContact::class)
            ->where('description', '<>', 'note_added')
            ->with('causer', 'subject')->latest()->get();

        return view('activities.timeline', compact('activities'));
    }

    public function notesForDentistContact(Request $request)
    {
        $activities = Activity::where('subject_id', $request->contact_id)
            ->where('subject_type', DentistContact::class)
            ->where('description', 'note_added')
            ->with('causer', 'subject')
            ->latest()->get();

        return view('activities.timeline', compact('activities'));
    }


}
