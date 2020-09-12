<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Lab;
use App\WeekDay;
use App\Timeframe;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeframeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('getWeekDays');
    }

    public function getWeekDays()
    {
        return WeekDay::all();
    }

    public function store(Request $request, $id)
    {
        $lab = Lab::find($id);
        if (Auth::user()->id == $lab->user_id) {
            if ($request['timeFrame']['id'] != '') {
                $timeframe = Timeframe::find($request['timeFrame']['id']);
            } else {
                $timeframe = new Timeframe();
            }
            $timeframe->lab_id = $id;
            if (isset($request['timeFrame']['day'])) {
                $timeframe->day_of_week = $request['timeFrame']['day'];
            } else if (isset($request['timeFrame']['day_of_week'])) {
                $timeframe->day_of_week = $request['timeFrame']['day_of_week'];
            }
            $timeframe->startTime = $request['timeFrame']['startTime'];
            $timeframe->endTime = $request['timeFrame']['endTime'];
            $timeframe->save();

            return $timeframe;
//            return "success – timeframe saved";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function getTimeframes($id)
    {
        if (Auth::usr()->hasRole('admin') || Auth::user()->hasRole('user')) {
            return Timeframe::where('lab_id', '=', $id)->get();
        } else {
            $lab = Lab::find($id);
            if (Auth::user()->id == $lab->user_id) {
                return Timeframe::where('lab_id', '=', $id)->get();
            }
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function destroy($id)
    {
        $timeframe = Timeframe::find($id);
        $lab = $timeframe->lab;
        if (Auth::user()->id == $lab->user_id) {
            $timeframe->delete();

            return "success – timeframe deleted";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }
}
