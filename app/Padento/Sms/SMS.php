<?php

namespace Padento\Sms;

use App\Date;
use App\Patient;
use Carbon\Carbon;
use PhpXmlRpc\Response;
use Activity;
use Log;
use testx\xmlrpc_client;
use testx\xmlrpcmsg;
use testx\xmlrpcval;

/**
 *
 */
class SMS
{
    // public static $smsServer = [
    //     'SIPGATE_SERVER'        => 'samurai.sipgate.net',
    //     'SIPGATE_PATH'          => 'RPC2',
    //     'SIPGATE_PROT'          => 'https',
    //     'SIPGATE_PORT'          => '443',
    //     'SIPGATE_SIPURI_PREFIX' => 'sip:',
    //     'SIPGATE_SIPURI_HOST'   => 'sipgate.net',
    //     'SIPGATE_USER'          => 'info@padento.de',
    //     'SIPGATE_PASS'          => 'pr9awWFXiKu3CARZEUMP',
    // ];

    function __construct()
    {
        // $this->$smsServer = $smsServer;
    }

    private static function sendsms($smsnumber, $smstext)
    {

        // Festlegen der Konfigurationswerte.
        if (!defined('SIPGATE_SERVER')) define('SIPGATE_SERVER', 'samurai.sipgate.net');
        if (!defined('SIPGATE_PATH')) define('SIPGATE_PATH', '/RPC2');
        if (!defined('SIPGATE_PROT')) define('SIPGATE_PROT', 'https');
        if (!defined('SIPGATE_PORT')) define('SIPGATE_PORT', '443');
        if (!defined('SIPGATE_SIPURI_PREFIX')) define('SIPGATE_SIPURI_PREFIX', 'sip:');
        if (!defined('SIPGATE_SIPURI_HOST')) define('SIPGATE_SIPURI_HOST', '@sipgate.net');

        if (!defined('SIPGATE_USER')) define('SIPGATE_USER', 'buero@padento.de');
        if (!defined('SIPGATE_PASS')) define('SIPGATE_PASS', 'pr9awWFXiKu3CARZEUMP');

        // Erstellen des xmlrpc clients.
        $xmlurl = SIPGATE_PROT . "://" . SIPGATE_USER . ":" . SIPGATE_PASS . "@" .
            SIPGATE_SERVER . ":" . SIPGATE_PORT . SIPGATE_PATH;

        $xmlclient = new xmlrpc_client($xmlurl);
        $xmlclient->setSSLVerifyPeer(false);

        // Rufnummer und Text für die SMS vorbereiten.
        $smsnumber = SIPGATE_SIPURI_PREFIX . $smsnumber . SIPGATE_SIPURI_HOST;
        // $smstext = substr($smstext, 0, 160);

        $val_a["LocalUri"]  = new xmlrpcval('sip:4915792347995@sipgate.net');
        $val_a["RemoteUri"] = new xmlrpcval($smsnumber);
        $val_a["TOS"]       = new xmlrpcval("text");
        $val_a["Content"]   = new xmlrpcval($smstext);
        //$val_a["Schedule"] = new xmlrpcval(iso8601_encode(NULL), "dateTime.iso8601");

        // dd($val_a);

        $val_s = new xmlrpcval();
        $val_s->addStruct($val_a);
        $v   = [];
        $v[] = $val_s;

        // Nachrichtenobjet erstellen.
        $m = new xmlrpcmsg('samurai.SessionInitiate', $v);
        // dd($m);

        // SMS senden.
        $r = $xmlclient->send($m);

        // Anzeigen von eventuellen Fehlern.
        if ($r->faultCode()) {
            return false;
        } else {
            return true;
        }
    }

    public static function testsms()
    {
        static::sendsms('491712873628', 'test');
    }

    public static function sendDateReminders()
    {
      $dates = Date::whereHas('patient', function ($q) {
          $q->where('phase', 3);
      })
          ->with(['patient.patientmeta', 'lab.labmeta'])
          ->whereDate('date', '=', Carbon::now()->tomorrow())->get();

        $message = "";
        $count   = 0;
        $sms     = 0;
        $status  = [];
        foreach ($dates as $date) {
            if ($date->lab->membership == 1 || $date->lab->membership == 4 || $date->lab->membership == 0) {
                if (($date->patient->patientmeta->mobile != '' || $date->patient->patientmeta->tel != '') && $date->patient->phase == 3 && $date->patient->id != "38952") {


                    $mobile = $date->patient->patientmeta->mobile != '' ? $date->patient->patientmeta->mobile : $date->patient->patientmeta->tel;

                    $mobile = str_replace('+', '', $mobile);
                    $mobile = str_replace(' ', '', $mobile);

//                  $mobile = phone_format($mobile, $country_code = 'DE');
                    if (!is_numeric($mobile)) {
                        $mobile = '';
                    }

                    if ($mobile != '') {
                        $time = Carbon::parse($date->date);
                        $time = $time->formatLocalized('%H:%M');
                        // $time = "{$time->hour}:{$time->hour}";
                        $message = "Ihr Padento-Termin: {$date->patient->patientmeta->salutation} {$date->patient->patientmeta->name}, morgen um {$time} Uhr erwartet Sie {$date->lab->labmeta->contact_person} in {$date->lab->labmeta->zip} {$date->lab->labmeta->city}, {$date->lab->labmeta->street} ihr Labor: {$date->lab->name}. Bei Rückfragen nutzen Sie bitte die Telefonnummer: {$date->lab->labmeta->tel}.";
                        echo "{$date->id} => {$date->date} => " . strlen($message) . " => {$mobile} => " . $message . "<br>";
                        if (\App::environment('local')) {
                            $sendedSms = true;
                        } else {
                            $sendedSms = static::sendsms($mobile, $message);
                        }

                        if ($sendedSms === true) {
                            $msg = "[SMSReminder] => Patient {$date->patient->patientmeta->name} wurde per SMS erinnert.";
                            $sms++;
                            // break;
                        } else {
                            $msg = "[SMSReminder] [Error] => Patient {$date->patient->patientmeta->name} wurde nicht per SMS erinnert.";
                        }
                        Log::info($msg);
//                            Activity::log($msg);

                        activity()->causedBy(adminUser())->performedOn($date->patient)->withProperties(['message' => $message])->log('sms_sent');
                    }
                }
                $count++;
            }
        }
        echo "<hr>{$sms}/{$count} Patienten haben eine Handynummer";
        echo "<pre>";
        print_r($status);
        echo "</pre>";

    }
}
