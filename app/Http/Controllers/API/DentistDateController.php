<?php

namespace App\Http\Controllers\API;

use App\Date;
use App\Services\Pagination;
use Auth;
use App\Lab;
use DateTime;
use App\DentistDate;
use FeedReader;
use App\DentistContact;
use Carbon\Carbon;
use App\EmployeeDate;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DentistDateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function feeds()
    {
        if (Auth::check()) {
            $data            = [];
            $feed            = FeedReader::read('http://padento.de/wissen');
            $data['padento'] = [
                'title'     => $feed->get_title(),
                'permalink' => $feed->get_permalink(),
                'items'     => [],
            ];
            foreach ($feed->get_items(0, 5) as $item) {
                $data['padento']['items'][] = [
                    'title'     => $item->get_title(),
                    'permalink' => $item->get_permalink(),
                    // 'description' => $item->get_description(),
                ];
            }
            $feed                 = FeedReader::read('http://www.rainerehrich.de/blog/rss.xml');
            $data['rainerehrich'] = [
                'title'     => $feed->get_title(),
                'permalink' => $feed->get_permalink(),
                'items'     => [],
            ];
            foreach ($feed->get_items(0, 5) as $item) {
                $data['rainerehrich']['items'][] = [
                    'title'     => $item->get_title(),
                    'permalink' => $item->get_permalink(),
                    // 'description' => $item->get_description(),
                ];
            }

            return response()->json($data);
        }
    }

    public function getDates($id)
    {
//        return DentistDate::where('lab_id', $id)->get();
        return Date::where('lab_id', $id)->whereNotNull('dentist_contact_id')->get();
    }

    public function deleteDate(Request $request)
    {
        $id = (string)$request->input('id');

        return DentistDate::where('id', $id)->delete();
    }

    public function myDates(Request $request)
    {
        $now       = [];
        $now_old   = [];
        $now_new   = [];
        $dates     = [];
        $old_dates = [];

        $user = auth()->user();

        $lab = $user->lab->count() ? $user->lab->first() : ($user->labs->count() ? $user->labs->first() : null);

        if(!$lab) {
            return ['dentist_dates' => $dates, 'old_dates' => $old_dates, 'today_old' => $now_old, 'today_new' => $now_new];
        }

        $dentistDates = Date::with('dentist_contact.dentistmeta')
            ->has('dentist_contact')
            ->where('lab_id', $lab->id)
            ->get();

        foreach ($dentistDates as $date) {

            $dateArray         = $date->toArray();
            $dateArray['user'] = $date->user->name;
            if ($date->dentist_contact_id) {
                if ($date->date < Carbon::today()->toDateTimeString()) {
                    $old_dates[] = $dateArray;
                }
                if ($date->date > Carbon::tomorrow()->toDateTimeString()) {
                    $dates[] = $dateArray;
                }
                if ($date->date > Carbon::today()->toDateTimeString() && $date->date < Carbon::tomorrow() && $date->date < Carbon::now()) {
                    $now_old[] = $dateArray;
                }
                if ($date->date > Carbon::today()->toDateTimeString() && $date->date <= Carbon::tomorrow() && $date->date > Carbon::now()) {
                    $now_new[] = $dateArray;
                }
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

        return ['dentist_dates' => $dates, 'old_dates' => $old_dates, 'today_old' => $now_old, 'today_new' => $now_new];
    }

    public function getEmployeeDate($id)
    {
        $user = auth()->user();

        if ($user->hasRole('user') || $user->hasRole('admin') || $user->hasRole('crm-user')) {
            $date = EmployeeDate::where('dentist_contact_id', $id)->count();
            if ($date > 0) {
                $date = EmployeeDate::where('dentist_contact_id', $id)->first();
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
            $date = EmployeeDate::where('dentist_contact_id', $request['dentist']['id'])->count();
            if ($date > 0) {
                $date = EmployeeDate::where('dentist_contact_id', $request['dentist']['id'])->first();
                if ($request->date == '') {
                    $date->delete();

                    return 'success - date deleted';
                }
            } else {
                $date = new EmployeeDate();
            }

            $datetosave = new DateTime($request->date);

            $date->user_id            = $user->id;
            $date->dentist_contact_id = $request['dentist']['id'];
            $date->date               = $datetosave->format('Y-m-d H:i');
            $date->save();

            activity()->causedBy($request->user())->performedOn(DentistContact::find($date->dentist_contact_id))->withProperties(['date' => $date->date])->log('employee_date_added');

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
