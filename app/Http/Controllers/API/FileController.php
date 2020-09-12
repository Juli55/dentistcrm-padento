<?php

namespace App\Http\Controllers\API;

use Auth;
use File;
use App\Lab;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'kontaktfoto' => 'file|mimes:jpeg,jpg,png',
            'logo' => 'file|mimes:jpeg,jpg,png',
            'bilder.*' => 'file|mimes:jpeg,jpg,png',
            'zert.*' => 'file|mimes:jpeg,jpg,png',
        ]);

        $lab = Lab::find($request->lab_id);

        if (Auth::user()->id == $lab->user_id || Auth::user()->hasRole('admin')) {
            if ($request->hasFile('kontaktfoto')) {
                $file = $request->file('kontaktfoto');

                $photo = $lab->images()->where('type', 'kontaktfoto')->first();

                if ($photo) {
                    $photo->delete();
                }

                $lab->uploadImage($file, 'kontaktfoto');
            };
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');

                $photo = $lab->images()->where('type', 'logo')->first();

                if ($photo) {
                    $photo->delete();
                }

                $lab->uploadImage($file, 'logo');
            }
            if ($request->hasFile('bilder')) {
                foreach ($request->file('bilder') as $image) {
                    if (!is_null($image)) {
                        $lab->uploadImage($image, 'bilder');
                    }
                }
            }
            if ($request->hasFile('zert')) {
                foreach ($request->file('zert') as $image) {
                    if (!is_null($image)) {
                        $lab->uploadImage($image, 'zert');
                    }
                }
            }

            return redirect()->back();
        }

        return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
    }

    public function destroy(Request $request)
    {
        $check = $request->file;

        $user = auth()->user();
        $lab = $user->lab->first();

        if (!$lab) {
            $lab = $user->labs->first();
        }

        if ($request->name == 'bild') {
            list($trash, $id) = preg_split('/bild[0-9]/', $check);
            list($id, $trash) = preg_split('/_neu.jpg/', $id);

            if (($lab && $lab->id == $id) || $user->hasRole('admin')) {
                $filename = $request->file;
                $path = public_path() . '/img/laborbilder/';
                if (File::exists($path . $filename)) {
                    File::delete($path . $filename);
                }

                return "success – image deleted";
            }

            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
        if ($request->name == 'zert') {
            list($trash, $id) = preg_split('/zert[0-9]/', $check);
            list($id, $trash) = preg_split('/_neu.jpg/', $id);

            if (($lab && $lab->id == $id) || $user->hasRole('admin')) {
                $filename = $request->file;
                $path = public_path() . '/img/zertifikate/';
                if (File::exists($path . $filename)) {
                    File::delete($path . $filename);
                }

                return "success – image deleted";
            }

            return response()->json(['status' => '403', 'message' => 'you are not allowed to do that']);
        }
    }
}
