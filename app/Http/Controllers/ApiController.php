<?php

namespace App\Http\Controllers;

use Auth;
use Event;
use App\User;
use App\Dentist;
use \App\Events\UserStatusChanged;
use Spatie\Activitylog\Models\Activity;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dentists()
    {
        return Dentist::with('dentistmeta')->get();
    }

    public function changeUserStatus($id)
    {
        if (Auth::user()->hasRole('admin')) {
            $user = User::find($id);

            if ($user->status == 'Aktiv') {
                $user->status = 'Deaktiviert';
            } else {
                $user->status = 'Aktiv';
            }

            $user->save();

            $msg = '[UserLogger] => ' . Auth::user()->name . ' schaltete den Nutzer ' . $user->name . ' (ID: ' . $user->id . ') auf ' . $user->status;
            Event::fire(new UserStatusChanged($msg));

            return redirect('/app/settings/users');
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function showLogs()
    {
        if (Auth::user()->hasROle('admin')) {
            return Activity::with('user')->latest()->limit(100)->get();  // TODO: modify this api
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function testFunction()
    {
        return Activity::with('user')->where('user_id', '=', '1')->where('text', 'LIKE', '%angemeldet%')->latest()->limit(100)->count(); // TODO: remove this
    }

    public function sortByDate($a, $b)
    {
        return strcmp($a["date"], $b["date"]);
    }

    public function unobtainable($id)
    {
        return ['Yeah'];
    }
}
