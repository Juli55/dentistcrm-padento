<?php

namespace App\Http\Controllers;

use App\Date;
use Illuminate\Http\Request;
use App\EventModel;
use App\Http\Requests;
use Calendar;
use Auth;
use Event;
use App\Events\TimeframesMissing;

class DentistCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id)
    {
        $user = auth()->user();

        $dentist = \App\DentistContact::find($id);
        $lab     = $dentist->lab;
        if (!$dentist->lab) {
            return response()->json(['status' => '403', 'message' => 'contact has no lab']);
        } else {
            $dates = Date::with('dentist_contact.dentistmeta', 'patient.patientmeta')->where('lab_id', $dentist->lab->id)->get();
        }

        $year       = \Carbon\Carbon::now()->year;
        $holidays   = file_get_contents('http://feiertage.jarmedia.de/api/?jahr=' . $year . '&nur_daten');
        $excludes   = [];
        $timeframes = [];
        $duration = '30';

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

            $arr = [
                'excludes'         => $excludes,
                'timeframes'       => $timeframes,
                'dates'            => $dates,
                'default_duration' => $duration,
                'holidays'         => $holidays,
                'id'               => $id,
                'patient_name'     => $dentist->dentistmeta->name,
                'type'             => 'dentist',
            ];

//            return $arr['dates'];

            return view('calendar.index', $arr);
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
    }
}
