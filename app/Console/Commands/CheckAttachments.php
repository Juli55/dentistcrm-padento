<?php

namespace App\Console\Commands;

use App\Email;
use App\Padento\Helper;
use App\Patient;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class CheckAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check attachments for patients.';

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
        foreach ($this->getPatientsWithoutAttachments() as $patient) {
            $this->sendMail($patient);
        }
    }

    public function getPatientsWithoutAttachments()
    {
//        $period = Settings::where('name', 'Upload Attachment Period')->first()->value;

        return Patient::whereDoesntHave('attachments')
            ->where('created_at', '>=', Carbon::today()->startOfDay()->subDay())
            ->where('created_at', '<', Carbon::today()->startOfDay())
            ->where('confirmed', true)
            ->with('patientmeta')
            ->latest()
            ->get();
    }

    private function sendMail(Patient $patient)
    {
        $email  = Email::where('name', 'UploadAttachments')->first();

        $message = Helper::markdown($email->body, null, $patient, null, $patient->token);
        $footer = $email->footer;
        $subject = $email->subject;

        if($patient->patientmeta->email) {
            Mail::send('emails.upload-attachments',
                ['body' => $message, 'footer' => $footer], function ($m) use ($patient, $subject) {
                    $m->from('buero@padento.de', 'Padento');
                    $m->to($patient->patientmeta->email, $patient->patientmeta->name)
                        ->subject($subject);
                });

            activity()->causedBy(adminUser())->performedOn($patient)->withProperties(['subject' => $subject, 'causedBySystem' => true])->log('mail_sent');
        }
    }
}
