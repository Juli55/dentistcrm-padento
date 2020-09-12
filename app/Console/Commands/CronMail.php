<?php

namespace App\Console\Commands;

use App\Email;
use App\Padento\Helper;
use Illuminate\Console\Command;
use Mail;

class CronMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronmail:lastday';

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
        $labs = \App\Lab::where('status', '=', 'aktiv')->where('membership', '!=', '1')->where('membership', '!=', '4')->get();

        foreach($labs as $lab) {
            $kontaktperson = $lab->labmeta->contact_person;
            if (\App::environment('local')) {
                $lab_mail = getDevMail();
            } else {
                $lab_mail = $lab->user->email;
            }
            $pats = \App\Patient::whereBetween('created_at', [\Carbon\Carbon::now()->subHours(48), \Carbon\Carbon::now()->subHours(24)])
                  ->where('lab_id', '=', $lab->id)
                  ->where('confirmed' , '==', '0')
                  ->where('archived', '!=', '1')
                  ->get();

            #echo "Labor: " . $lab->id . "\n";

            if(sizeOf($pats) > 0){
                $email = Email::where('name', 'LastDay')->first();

                $body = Helper::markdown($email->body, $lab);

                $patients = '';

                foreach($pats as $pat) {
                    if (is_object($pat)) {
                        if (is_object($pat->patientmeta))  $patients .= "<li>{$pat->patientmeta->name}</li>";
                    }
                }

                $body = str_replace('[patients]', $patients, $body);

                Mail::send( 'emails.lastday', ['body' => $body], function($message) use ($kontaktperson, $lab_mail, $email)
                {
                    $message->from('buero@padento.de', 'Padento');
                    $message->getHeaders()->addTextHeader('X-MC-Tags', 'Cronjob, unbestätigte Kontakte, Labor, unbestätigt');
                    $message->to($lab_mail, $kontaktperson)->subject($email->subject);
                });

                foreach($pats as $pat) {
                    activity()->causedBy(adminUser())->performedOn($pat)->withProperties(['subject' => $email->subject, 'causedBySystem' => true, 'mail' => $email, 'receiver' => $lab->name])->log('mail_sent');
                }

                #echo $pats . "\n";
                #echo $text . "\n";
            }
        }
    }
}
