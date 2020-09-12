<?php

namespace App\Listeners;

use App\Events\ValidationFailed;
use App\Services\Setting;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendErrorNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ValidationFailed  $event
     * @return void
     */
    public function handle(ValidationFailed $event)
    {
        $input = $event->input;

        $sendTo = (new Setting)->getErrorEmailAddress();

        if ($sendTo) {
            try {
                Mail::send('emails.validation-failed', ['input' => $input], function ($message) use ($sendTo) {
                    $message->to($sendTo)->subject('Formular wurde falsch ausgefüllt.');
                    $message->from('info@padento.de', 'Padento');
                });
            } catch (Exception $e) {
                logger('mail error...');
            }
        }
    }
}
