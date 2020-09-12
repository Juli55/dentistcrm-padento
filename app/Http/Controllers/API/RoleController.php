<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Role;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allRoles()
    {
        return Role::all();
    }

    public function setUserRole($user, $role)
    {
        if (Auth::user()->hasRole('admin')) {
            $user = User::find($user);
            $user->roles()->sync([$role]);

            return "changed userrole";
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

}
