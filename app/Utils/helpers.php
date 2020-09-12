<?php

/**
 * Returns UUID of 32 characters
 *
 * @return string
 */
function generateUUID()
{
    $currentTime = (string)microtime(true);

    $randNumber = (string)rand(10000, 1000000);

    $shuffledString = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");

    return md5($currentTime . $randNumber . $shuffledString);
}

/**
 * Mailer.
 *
 * @param $mailName
 * @param \App\Patient|null $patient
 * @param \App\Lab|null $lab
 * @param \App\Date|null $date
 * @param string|null $token
 *
 * @return \App\Services\Mailer
 */
function mailer($mailName, $patient = null, $lab = null, $date = null, $token = null)
{
    return (new \App\Services\Mailer($mailName, $patient, $lab, $date, $token));
}

/**
 * Get contact phase/phases
 *
 * @param null $phaseId
 * @return array|mixed
 */
function contact_phases($phaseId = null)
{
    $phases = [
        1           => 'Neu',
        2           => 'Kontaktaufnahme',
        3           => 'Labor-Termin vereinbart',
        4           => 'In Betreuung',
        5           => 'Auftrag erhalten',
        6           => 'Kein Interesse',
        'undefined' => '',
    ];

    return $phaseId ? $phases[$phaseId] : $phases;
}

function dentist_phases($phaseId = null)
{
    $phases = [
        1           => 'Fremd',
        2           => 'Vertrauen',
        3           => 'Beziehung',
        4           => 'Testkunde',
        5           => 'B-Kunde',
        6           => 'A-Kunde',
        'undefined' => '',
    ];

    if (!isset($phases[$phaseId])) return 'undefined';
    return $phaseId ? $phases[$phaseId] : $phases;
}


/**
 * Get admin user.
 *
 * @return mixed
 */
function adminUser()
{
    return \App\User::find(1);
}

function getDevMail()
{
    $devMail = (new \App\Services\Setting())->getDevEmailAddress();

    if (!$devMail) {
        $devMail = env('DEV_MAIL', 'info@padento.de');
    }

    return $devMail;
}

function pathToUploadedFile($path, $public = false)
{
    $name = File::name($path);

    $extension = File::extension($path);

    $originalName = $name . '.' . $extension;

    $mimeType = File::mimeType($path);

    $size = File::size($path);

    $error = null;

    $test = $public;

    $object = new Illuminate\Http\UploadedFile($path, $originalName, $mimeType, $size, $error, $test);

    return $object;
}

function getLocation($address, $lang = 'de')
{
    logger()->info('GOOGLE_API_CALL_' . \Carbon\Carbon::now()->format('Y_m_d'));

    $client = new \GuzzleHttp\Client();
    $apiKey = config('services.maps.key');

    $country = ($lang == 'at') ? 'Österreich' : 'Deutschland';

    $address = $address . ' ' . $country;

    $response = json_decode($client->get("https://maps.googleapis.com/maps/api/geocode/json?address=$address&region=$lang&key=$apiKey")->getBody(), true);

    if ($response['status'] != 'OK') {
        return null;
    }

    $result = $response['results'][0];
    $geometry = $result['geometry'];

    $city = $state = null;

    foreach ($result['address_components'] as $addressComponent) {
        if (in_array('locality', $addressComponent['types']) || in_array('administrative_area_level_3', $addressComponent['types'])) {
            $city = array_get($addressComponent, 'long_name');
        }

        if (in_array('administrative_area_level_1', $addressComponent['types'])) {
            $state = array_get($addressComponent, 'long_name');
        }
    }

    return [
        'longitude'         => $geometry['location']['lat'],
        'latitude'          => $geometry['location']['lng'],
        'location_type'     => $geometry['location_type'],
        'formatted_address' => $result['formatted_address'],
        'city'              => $city,
        'state'             => $state,
    ];
}

function mailSignature()
{
    return '<div style="font-size: 12px; text-align: center">'
        . '<br><p>Ehrich Dental Consulting GmbH - Torplatz 1- 29223 Celle - Telefon 05141-9780976 - E-Mail: <a href="mailto:buero@padento.de">buero@padento.de</a><br>'
        . 'Amtsgericht Lüneburg, HRB 205 391 - UST-ID Nr. DE294076454 -<br>'
        . 'Geschäftsführende Gesellschafterin: Marina Ehrich<br>'
        . 'Web: <a href="https://www.padento.de">https://www.padento.de</a><br>'
        . '<br>Diese E-Mail enthält vertrauliche und/oder rechtlich geschützte Informationen.<br>'
        . '<br>Wenn Sie nicht der richtige Adressat sind, oder diese E-Mail irrtümlich erhalten haben, informieren Sie bitte den Absender und löschen Sie diese Mail. Das unerlaubte Kopieren sowie die unbefugte Weitergabe dieser E-Mail und der darin enthaltenen Informationen sind nicht gestattet.</p>';
}

function dsgvoMessage()
{
    return '<br><p style="text-align: center"><a href="' . route('account.remove') . '" style="color: #333333;">Ich möchte meine Daten bei Padento löschen</a></p></div>';
}