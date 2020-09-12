<?php

namespace App\Console\Commands;

use App\Email;
use App\Padento\Helper;
use Illuminate\Console\Command;

class CronDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crondates:nextday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $labs = \App\Lab::where('status', 'aktiv')->get();
        foreach ($labs as $lab) {
            if (\App::environment('local')) {
                $lab_mail = getDevMail();
            } else {
                $lab_mail = $lab->user->email;
            }
            echo "LabMail => $lab_mail";
            $parsed = [];
            $dates = $lab->dates;
            $kontaktperson = $lab->labmeta->contact_person;
            if (sizeOf($dates) > 0) {
                foreach ($dates as $date) {
                    if ($date->patient
                        && $date->patient->phase === 3
                        && $date->date > \Carbon\Carbon::now()->subHours(72)
                        && $date->date < \Carbon\Carbon::today()
                        && $date->archived == '0'
                        && $date->date != '0000-00-00 00:00:00') {
                        $parsed[] = $date;
                    }
                }
                $size = sizeOf($parsed);
                if ($size >= 1) {
                    if ($lab->membership == '1' || $lab->membership == '4') {
                        echo "sending mail for $lab->name #$lab->id because parsed is $size \n";

                        $email = Email::where('name', 'NextDay')->first();

                        $body = Helper::markdown($email->body, $lab);

                        $dateString = '';

                        foreach ($parsed as $date) {
                            $url = url("app/kontakt/{$date->patient_id}");
                            $dateString .= sprintf("<li>%s - %s <a href='%s'>Direktlink zum Kontakt</a></li>", $date->patient->patientmeta->name, $date->date, $url);
                        }

                        $body = str_replace('[dates]', $dateString, $body);

                        \Mail::send('emails.nextday', ['body' => $body], function ($msg) use ($lab_mail, $lab, $kontaktperson) {
                            $msg->from('buero@padento.de', 'Padento');
                            $msg->getHeaders()->addTextHeader('X-MC-Tags', 'Cronjob, Labor, vergangende Termine');
                            $msg->to($lab_mail, $kontaktperson)->subject('[Padento] ' . $lab->user->email);
                        });
                        foreach ($parsed as $p) {
                            activity()->causedBy(adminUser())->performedOn($p->patient)->withProperties(['subject' => $email->subject, 'causedBySystem' => true, 'mail' => $email, 'receiver' => $lab->name])->log('mail_sent');
                            $p->archived = '1';
                            $p->save();
                            echo $p->date . " => " . $p->lab_id . "\n";
                        }
                    }
                }
            }
        }
    }
}
