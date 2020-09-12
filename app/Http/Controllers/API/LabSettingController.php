<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Lab;
use DateTime;
use App\LabSetting;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSettings()
    {
        $labs = [];
        $alllabs = Lab::with('labmeta')
            ->with('settings')
            ->with('timeframes')
            ->where('user_id', '=', Auth::user()->id)->get();

        if (count($alllabs) == 0)
            $alllabs = Lab::wherehas('users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
                ->with('labmeta')
                ->with('settings')
                ->with('timeframes')
                ->get();

        foreach ($alllabs as $lab) {
            if ($lab->membership == '1' || $lab->membership == '4' || $lab->membership == '5' || $lab->membership == '0') {
                $labs[] = $lab;
            }
        }

        return $labs;
    }

    public function storeSetting(Request $request, $id)
    {
        $setting = LabSetting::find($id);
        $lab = Lab::find($setting->lab_id);
        if (Auth::user()->id == $lab->user_id && $lab->membership == '1') {
            $setting->value = $request->value;
            $setting->save();

            return "success – labsetting saved";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function removeSetting($id)
    {
        $setting = LabSetting::find($id)->delete();

        return "success – setting removed";
    }

    public function excludeDay($date)
    {
        $labs = Auth::user()->lab;
        if (count($labs) == 0) {
            $labs = Auth::user()->labs;
        }
        $date = explode(',', $date);
        foreach ($labs as $lab) {
            if ($date[1] == '') {
                $from = DateTime::createFromFormat("d.m.Y", $date[0]);
                $savedate = $from->format('Y-m-d');
                $setting = LabSetting::firstOrCreate(['name' => 'excluded_day', 'value' => $savedate, 'lab_id' => $lab->id]);
                $setting->description = "An diesem Tag möchte ich keinen Termin";
                $setting->save();
            } else {
                $from = DateTime::createFromFormat("d.m.Y", $date[0]);
                $till = DateTime::createFromFormat("d.m.Y", $date[1]);
                $diff = $till->diff($from)->format("%a");
                $test = [];
                for ($i = 0; $i <= $diff; $i++) {
                    $tempdate = $from;
                    if ($i == 0) {
                        $savedate = $tempdate->format('Y-m-d');
                    } else {
                        $savedate = $tempdate->modify('+1 day')->format('Y-m-d');
                    }
                    $setting = LabSetting::firstOrCreate(['name' => 'excluded_day', 'value' => $savedate, 'lab_id' => $lab->id]);
                    $setting->description = "An diesem Tag möchte ich keinen Termin";
                    $setting->save();
                    // $test[] = $savedate;
                }
                // return $test;
            }
        }

        return "success – labsetting saved";
    }

    public function getTimeFrames($id)
    {
        return LabSetting::where('lab_id', $id)->where('name', 'timeframe')->get();
    }
}
