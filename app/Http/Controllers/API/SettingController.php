<?php

namespace App\Http\Controllers\API;

use Auth;
use Event;
use App\Email;
use App\Settings;
use App\PropPivot;
use App\PatientProp;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Events\PropertiesChanged;
use App\Events\SystemSettingChanged;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function emailSettings()
    {
        if (Auth::user()->hasRole('admin')) {
            return Email::orderBy('sort', 'ASC')->get();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function saveEmail(Request $request, $id)
    {
        if (Auth::user()->hasRole('admin')) {
            $email = Email::find($id);

            #$email->body = str_replace("\n","<br/>", $request->body);
            #$email->footer = str_replace("\n", "<br/>", $request->footer);
            $email->body = $request->body;
            $email->footer = $request->footer;
            $email->subject = $request->subject;
            $email->short_description = $request->short_description;
            $email->description = $request->description;
            // $email->footer = $request->footer;
            $email->save();

            return "Email saved";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function getSettings()
    {
        if (Auth::user()->hasRole('admin')) {
            $hiddenSettings = [10, 11, 12, 15, 16, 18, 19, 20, 21, 22, 32, 33 ];
            return Settings::whereNotIn('id', $hiddenSettings)->get();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function storeSettings(Request $request, $id)
    {
        if (Auth::user()->hasRole('admin')) {
            $setting = Settings::find($id);

            $setting->value = $request->value;

            $setting->save();

            $msg = '[SystemLogger] => ' . Auth::user()->name . ' änderte die Systemeinstellung ' . $setting->name;
            Event::fire(new SystemSettingChanged($msg));

            return "Setting saved";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function getProperties()
    {
        if (Auth::user()->hasRole('admin')) {
            return PatientProp::with('user')->get();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function storeProperty(Request $request, $id)
    {
        $prop = PropPivot::find($id);

        $prop->value = $request->value;

        $prop->save();

        return "property saved";
    }

    public function singleProperty($id)
    {
        return PatientProp::with('user')->where('id', '=', $id)->first();
    }

    public function newProperty(Request $request)
    {
        $prop = new PatientProp();

        $prop->name = $request->name;
        $prop->default = $request->default;
        $prop->type = $request->type;
        $prop->category_id = '-';
        $prop->user_id = Auth::user()->id;
        $prop->status = 'Deaktiviert';

        $prop->save();

        Event::fire(new PropertiesChanged());

        $msg = '[PropertyLogger] => ' . Auth::user()->name . ' erstellte die dynamische Eigenschaft ' . $prop->name;
        Event::fire(new SystemSettingChanged($msg));

        return "new property persisted";
    }

    public function storePropertyTemplate(Request $request, $id)
    {
        $prop = PatientProp::find($id);

        $prop->name = $request->name;
        $prop->default = $request->default;
        $prop->type = $request->type;
        $prop->status = $request->status;

        $prop->save();

        Event::fire(new PropertiesChanged());

        $msg = '[PropertyLogger] => ' . Auth::user()->name . ' änderte die dynamische Eigenschaft ' . $prop->name;
        Event::fire(new SystemSettingChanged($msg));

        return "property saved";
    }

    public function changePropertyStatus($id)
    {
        $prop = PatientProp::find($id);

        if ($prop->status == 'Aktiv') {
            $prop->status = 'Deaktiviert';
        } else {
            $prop->status = 'Aktiv';
        }

        $prop->save();

        Event::fire(new PropertiesChanged());

        $msg = '[PropertyLogger] => ' . Auth::user()->name . ' schaltete die dynamische Eigenschaft ' . $prop->name . ' auf ' . $prop->status;
        Event::fire(new SystemSettingChanged($msg));

        return redirect('/dashboard#!/admin/settings/properties');
    }
}
