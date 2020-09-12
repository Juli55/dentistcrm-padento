<?php

namespace App\Services;

class Application
{
    public static function scriptVariables()
    {
        $user = auth()->user();

        if ($user) {
            $user->load('roles', 'lab');
        }

        return [
            'csrfToken' => csrf_token(),
            'env'       => config('app.env'),
            'user'      => $user,
            'userId'    => $user ? $user->id : null,
            'signedIn'  => $user ? true : false,
        ];
    }
}