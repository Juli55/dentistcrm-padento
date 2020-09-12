<?php

namespace App\Http\Controllers\API;

use Mail;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function sendTestmail(Request $request, $id)
    {
        $mail = $request->mail;
        if ($id == 1) {
            Mail::send('emails.testmail', ['name' => 'Test', 'body' => $mail['body'], 'footer' => $mail['footer']], function ($message) use ($request) {
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Padento v2.0, Testmail');
                $message->to($request->to, 'Marc')->subject($request->to);
                $message->from('info@padento.de', 'PadentoTest');
            });
        } else if ($id == 3) {
            Mail::send('emails.kontakt1', ['token' => '1234', 'name' => 'Test', 'body' => $mail['body'], 'footer' => $mail['footer']], function ($message) use ($request) {
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Padento v2.0, Testmail');
                $message->to($request->to, 'Marc')->subject($request->to);
                $message->from('info@padento.de', 'PadentoTest');
            });
        } else if ($id == 4) {
            Mail::send('emails.kontakt2', ['laborname' => 'Test-Labor', 'id' => '23', 'labor' => 'testlabor', 'kontaktperson' => 'Die Kontaktperson', 'name' => 'Test', 'body' => $mail['body'], 'footer' => $mail['footer']], function ($message) use ($request) {
                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Padento v2.0, Testmail');
                $message->to($request->to, 'Marc')->subject($request->to);
                $message->from('info@padento.de', 'PadentoTest');
            });
        }

        return "Mail versendet...";
    }

}
