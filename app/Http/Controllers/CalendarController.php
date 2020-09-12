<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventModel;
use App\Http\Requests;
use Calendar;
use Auth;
use Event;
use App\Events\TimeframesMissing;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function index ()
    // {
    //     $year = \Carbon\Carbon::now()->year;
    //     $holidays = file_get_contents('http://feiertage.jarmedia.de/api/?jahr=' . $year . '&nur_daten');
    //     $excludes = [];
    //     $timeframes = [];
    //     $dates = [];
    //     $duration = '30';
    //     $labs = Auth::user()->lab;
    //     foreach($labs as $lab) {
    //         foreach($lab->settings as $set) {
    //             if($set->name == 'excluded_day') {
    //                 $excludes[] = $set->value;
    //             }
    //             if($set->name == 'default_date_duration') {
    //                 $duration = $set->value;
    //             }
    //         }
    //         foreach($lab->timeframes as $time) {
    //             $timeframes[] = $time;
    //         }
    //         foreach($lab->latestdates as $date) {
    //             $dates[] = $date;
    //         }
    //     }

    //     return view('calendar.lab-index', [
    //         'excludes'         => $excludes,
    //         'timeframes'       => $timeframes,
    //         'dates'            => $dates,
    //         'default_duration' => $duration,
    //         'holidays'         => $holidays
    //     ]);
    // }

    public function show($id)
    {
        $user = auth()->user();

        $pat = \App\Patient::find($id);
        if (!$pat) {
            return response()->json(['status' => '403', 'message' => 'contact does not exist']);
        }
        $lab = $pat->lab;
        if (!$pat->lab) {
            return response()->json(['status' => '403', 'message' => 'contact has no lab']);
        }

        $year       = \Carbon\Carbon::now()->year;
        $holidays   = file_get_contents('http://feiertage.jarmedia.de/api/?jahr=' . $year . '&nur_daten');
        $excludes   = [];
        $timeframes = [];
        $dates      = [];
        $duration   = '30';

        if (($user->hasRole('user') || $user->hasRole('admin') || $user->hasRole('crm-user') || $user->id == $lab->user_id) || ($user->id == $lab->users()->wherePivot('user_id', $user->id)->exists())) {
            foreach ($lab->settings as $set) {
                if ($set->name == 'excluded_day') {
                    $excludes[] = $set->value;
                }
                if ($set->name == 'default_date_duration') {
                    $duration = $set->value;
                }
            }
            foreach ($lab->timeframes as $time) {
                $timeframes[] = $time;
            }
            // foreach($lab->latestdates as $date) {
            //     $dates[] = $date;
            // }
            // dd('test');
            // return $lab->latestdates;
            // return $dates;

//            if (sizeOf($timeframes) > 0) {

            return view('calendar.index', [
                'excludes'         => $excludes,
                'timeframes'       => $timeframes,
                'dates'            => $lab->latestdates,
                'default_duration' => $duration,
                'holidays'         => $holidays,
                'id'               => $id,
                'patient_name'     => $pat->patientmeta->name,
                'name'             => $pat->patientmeta->name,
                'type'             => 'contact',
            ]);
//            }
//            else {
//                Event::fire(new TimeframesMissing($lab));
//
//                return "<h2 style=\"text-align:center;\">Dieses Labor hat noch keine Erreichbarkeiten angegeben.<br/>Bitte bei <a href='mailto:" . $lab->user->email . "'>" . $lab->name . " [" . $lab->user->email . "] </a> melden.</h2>";
//            }
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
    }
}
