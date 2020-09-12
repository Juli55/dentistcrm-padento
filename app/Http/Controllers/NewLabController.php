<?php

namespace App\Http\Controllers;

use Auth;
use Uuid;
use App\Lab;
use App\LabMeta;
use App\User;
use App\Http\Requests;
use App\Http\Requests\LabRequest;
use Illuminate\Http\Request;

class NewLabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.newlab');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(LabRequest $request)
    {
        $lab = $request->all()['lab'];

        $request->merge(['slug' => str_slug($lab['lab']['name'])]);

        $this->validate($request, [
            'slug' => 'unique:labs',
        ]);

        // return $lab['lab']['labmeta']['contact_person'];

        $user = User::create([
            'name'       => $lab['lab']['labmeta']['contact_person'],
            'email'      => $lab['user']['email'],
            'password'   => bcrypt($lab['user']['password']),
            'allowlogin' => 'allow',
        ]);

        $user->roles()->attach('3');

        if($request->dsgvo) {
            $user->acceptDsgvo();
        }

        $geo = getLocation($lab['lab']['labmeta']['zip'], $lab['lab']['labmeta']['country_code']);

        $newLab = Lab::create([
            'user_id'     => $user->id,
            'name'        => $lab['lab']['name'],
            'status'      => 'inaktiv',
            'google_city' => $lab['lab']['labmeta']['city'],
            'slug'        => str_slug($lab['lab']['name'], '-'),
            'directtoken' => UUid::generate('4'),
            'lat'         => $geo['latitude'],
            'lon'         => $geo['longitude'],
        ]);

        $labMeta = LabMeta::create([
            'contact_person' => $lab['lab']['labmeta']['contact_person'],
            'contact_mail'   => $lab['user']['email'],
            'street'         => $lab['lab']['labmeta']['street'],
            'city'           => $lab['lab']['labmeta']['city'],
            'zip'            => $lab['lab']['labmeta']['zip'],
            'country_code'   => $lab['lab']['labmeta']['country_code'],
        ]);

        $newLab->labmeta()->save($labMeta);

        if (Auth::guest()) {
            Auth::loginUsingId($user->id); // Test
        }

        return redirect('app/labore/' . $newLab->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function lookup($string, $country_code = 'de')
    {
        $string  = str_replace(" ", "+", urlencode($string));
        $country = 'Deutschland';
        if ($country_code == 'at') {
            $country = 'Österreich';
        }
        $string      = $string . '+' . $country;
        $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&region=" . $country_code . "&language=de";
        $ch          = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        if ($response['status'] != 'OK') {
            return null;
        }
        // try {
        //     $formatted_address = $response['results'][0]['formatted_address'];
        $geometry = $response['results'][0]['geometry'];
        //     $city = explode(' ', explode(',',$formatted_address)[0]);
        //     dd($city);
        // } catch (Exception $e) {
        //     $city = '';
        // }
        $longitude = $geometry['location']['lat'];
        $latitude  = $geometry['location']['lng'];
        $array     = [
            'latitude'  => $geometry['location']['lng'],
            'longitude' => $geometry['location']['lat'],
            // 'location_type'     => $geometry['location_type'],
            // 'formatted_address' => $formatted_address,
            // 'city'              => $city
        ];

        return $array;
    }

}
