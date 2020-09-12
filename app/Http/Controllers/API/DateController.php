<?php

namespace App\Http\Controllers\API;

use App\DentistContact;
use App\Services\Pagination;
use Auth;
use App\Lab;
use DateTime;
use App\Date;
use FeedReader;
use App\Patient;
use Carbon\Carbon;
use App\EmployeeDate;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDates($id)
    {
        return Date::where('lab_id', $id)->whereNotNull('patient_id')->get();
    }

    public function deleteDate(Request $request)
    {
        $id = (string)$request->input('id');

        $date = Date::where('id', $id)->firstOrFail();

        activity()->causedBy($request->user())->performedOn(Patient::find($date->patient_id))->withProperties(['date' => $date])->log('date_deleted');

        $date->delete();

        return ['success'];
    }

    public function deleteDentistDate(Request $request)
    {
        $id = (string)$request->input('id');

        $date = Date::where('id', $id)->firstOrFail();

        activity()->causedBy($request->user())->performedOn(DentistContact::find($date->dentist_contact_id))->withProperties(['date' => $date])->log('date_deleted');

        $date->delete();

        return ['success'];
    }

    public function myDates(Request $request)
    {
        $now       = [];
        $now_old   = [];
        $now_new   = [];
        $dates     = [];
        $old_dates = [];
        if (Auth::user()->hasRole('user') || Auth::user()->hasRole('admin')) {
            $checkdates = EmployeeDate::has('patient')->with(['user'])->get();
            /* $employeedates=EmployeeDate::has('patient')->with(['user'])->get();
             $checkdates->merge($employeedates);*/
            foreach ($checkdates as $date) {
                if ($date->date < Carbon::today()->toDateTimeString()) {
                    $patient = Patient::with(['patientmeta'])->where('id', $date->patient_id)->first();

                    $tempdate             = new \stdClass();
                    $tempdate->date       = $date->date;
                    $tempdate->patient    = $patient;
                    $tempdate->patient_id = $date->patient_id;
                    $tempdate->user       = $date->user->name;
                    $tempdate->phase      = $patient->phase;

                    $old_dates[] = $tempdate;
                }
                if ($date->date > Carbon::tomorrow()->toDateTimeString()) {
                    $patient = Patient::with(['patientmeta'])->where('id', $date->patient_id)->first();

                    $tempdate             = new \stdClass();
                    $tempdate->date       = $date->date;
                    $tempdate->patient    = $patient;
                    $tempdate->patient_id = $date->patient_id;
                    $tempdate->user       = $date->user->name;
                    $tempdate->phase      = $patient->phase;

                    $dates[] = $tempdate;
                }
                if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date < Carbon::now()) {
                    $patient = Patient::with(['patientmeta'])->where('id', $date->patient_id)->first();

                    $tempdate             = new \stdClass();
                    $tempdate->date       = $date->date;
                    $tempdate->patient    = $patient;
                    $tempdate->patient_id = $date->patient_id;
                    $tempdate->user       = $date->user->name;
                    $tempdate->phase      = $patient->phase;

                    $now_old[] = $tempdate;
                }
                if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date > Carbon::now()) {
                    $patient = Patient::with(['patientmeta'])->where('id', $date->patient_id)->first();

                    $tempdate             = new \stdClass();
                    $tempdate->date       = $date->date;
                    $tempdate->patient    = $patient;
                    $tempdate->patient_id = $date->patient_id;
                    $tempdate->user       = $date->user->name;
                    $tempdate->phase      = $patient->phase;

                    $now_new[] = $tempdate;
                }
            }
            $old_dates = new Pagination($old_dates,
                $request['oldDate']['per_page'],
                $request['oldDate']['current_page']);

            $dates   = new Pagination($dates, $request['date']['per_page'],
                $request['date']['current_page']);
            $now_old = new Pagination($now_old, $request['newOldDate']['per_page'],
                $request['newOldDate']['current_page']);
            $now_new = new Pagination($now_new, $request['newNowDate']['per_page'],
                $request['newNowDate']['current_page']);

            return ['dates' => $dates, 'old_dates' => $old_dates, 'today_old' => $now_old, 'today_new' => $now_new];
        } else {
            $user = auth()->user();

            $labs = Lab::with('dates')->where('user_id', '=', $user->id)->get();

            if (count($labs) == 0) {
                $labs = $user->labs()->get();
            }
            foreach ($labs as $lab) {
                foreach ($lab->dates as $date) {

                    $dateArray         = $date->toArray();
                    $dateArray['user'] = $date->user->name;

                    if ($request->get('dentists')) { // if this for the appoihtment wiendow in the dashboard page  then get all appoinbtmetns contacts + dentists
                        if ($date->date < Carbon::today()->toDateTimeString()) {
                            $old_dates[] = $dateArray;
                        }
                        if ($date->date > Carbon::tomorrow()->toDateTimeString()) {
                            $dates[] = $dateArray;
                        }
                        if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date < Carbon::now()) {
                            $now_old[] = $dateArray;
                        }
                        if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date > Carbon::now()) {
                            $now_new[] = $dateArray;
                        }
                    } else { //if this in the appointemnts page  then get only the contact appoihtmetns WITHOUT dentists appothmetns
                        if (!$date->dentist_contact_id) {
                            if ($date->date < Carbon::today()->toDateTimeString()) {
                                $old_dates[] = $dateArray;
                            }
                            if ($date->date > Carbon::tomorrow()->toDateTimeString()) {
                                $dates[] = $dateArray;
                            }
                            if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date < Carbon::now()) {
                                $now_old[] = $dateArray;
                            }
                            if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date > Carbon::now()) {
                                $now_new[] = $dateArray;
                            }
                        }
                    }
                }
            }
            usort($dates, [$this, 'sortByDate']);

            $old_dates = new Pagination($old_dates,
                $request['oldDate']['per_page'],
                $request['oldDate']['current_page']);

            $dates   = new Pagination($dates, $request['date']['per_page'],
                $request['date']['current_page']);
            $now_old = new Pagination($now_old, $request['newOldDate']['per_page'],
                $request['newOldDate']['current_page']);
            $now_new = new Pagination($now_new, $request['newNowDate']['per_page'],
                $request['newNowDate']['current_page']);

            return ['dates' => $dates, 'old_dates' => $old_dates, 'today_old' => $now_old, 'today_new' => $now_new];
        }
    }

    public function getEmployeeDate($id)
    {
        $user = auth()->user();

        if ($user->hasRole('user') || $user->hasRole('admin') || $user->hasRole('crm-user')) {
            $date = EmployeeDate::where('patient_id', $id)->count();
            if ($date > 0) {
                $date = EmployeeDate::where('patient_id', $id)->first();
                $date = new DateTime($date->date);

                return $date->format('d.m.Y H:i');
            } else {
                return '';
            }
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
    }

    public function saveEmployeeDate(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('user') || $user->hasRole('admin')) {
            $date = EmployeeDate::where('patient_id', $request['patient']['id'])->count();
            if ($date > 0) {
                $date = EmployeeDate::where('patient_id', $request['patient']['id'])->first();
                if ($request->date == '') {
                    $date->delete();

                    activity()->causedBy($request->user())->performedOn(Patient::find($date->patient_id))->withProperties(['date' => $date])->log('employee_date_deleted');

                    return 'success - date deleted';
                }
            } else {
                $date = new EmployeeDate();
            }

            $datetosave = new DateTime($request->date);

            $date->user_id    = $user->id;
            $date->patient_id = $request['patient']['id'];
            $date->date       = $datetosave->format('Y-m-d H:i');
            $date->save();

            activity()->causedBy($request->user())->performedOn(Patient::find($date->patient_id))->withProperties(['date' => $date->date])->log('employee_date_added');

            return 'success – date saved';
        } else {
            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
    }

    public function sortByDate($a, $b)
    {
        return strcmp($a["date"], $b["date"]);
    }
}












































































