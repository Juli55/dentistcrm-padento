<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\ZipCode;

class ZipCodeController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->get('query');

        return ZipCode::where('country_id', $request->country_id)
            ->where('zip_code', 'like', "%$query%")
            ->take(10)
            ->get();
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'zip_code' => 'required',
            'country_id' => 'required',
        ]);
        return ZipCode::create($request->only('zip_code', 'country_id'));
    }


    public function get($country_id = 1)
    {
        return ZipCode::where('country_id', $country_id)->get();
    }

    public function show($id)
    {
        return ZipCode::findOrFail($id);
    }

    public function destroy($id)
    {
        return ZipCode::destroy($id);
    }

}




