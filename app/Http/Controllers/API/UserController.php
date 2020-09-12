<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            return User::with('labs')->with('roles')->get();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function view($id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->id == $id) {
            return User::with('labs')->with('roles')->with('metas')->where('id', '=', $id)->first();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->id == $id) {
            $user = User::find($id);

            $user->name = $request->name;
            if ($user->email != $request->email) {
                $user->email = $request->email;
            };

            if ($request->pw_two != '') {
                if ($request->pw_one == $request->pw_two) {
                    $user->password = \Hash::make($request->password);
                }
            }

            $user->save();

            return "updated user";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function updateUserPassword(Request $request, $id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->id == $id) {
            $user = User::find($id);

            $this->validate($request, [
                'metas.firstname' => 'max:64',
                'metas.lastname'  => 'max:128',
            ]);

            $user->name = $request->name;
            if ($user->email != $request->email) {
                $user->email = $request->email;
            };

            $user->password = bcrypt($request->password);

            $user->save();

            $oldmetas = $user->metas;

            if ($request['metas'] != '') {
                $oldmetas->firstname = $request['metas']['firstname'];
                $oldmetas->lastname  = $request['metas']['lastname'];
                $oldmetas->save();
            }

            return "updated user";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function delete(Request $request, $id)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->id == $id) {
            $user = User::find($id);
            $user->delete();
        }
    }

    public function whoami()
    {
        if (!Auth::check()) {
            return abort(404);
        }

        return User::with(['roles', 'lab'])->where('id', '=', Auth::id())->first();
    }
}
