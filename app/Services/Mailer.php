<?php

namespace App\Services;

use App\Dentist;
use Log;
use Mail;
use App\Lab;
use Activity;
use App\Date;
use App\Email;
use Exception;
use App\Patient;
use App\Padento\Helper;

class Mailer
{
    /** @var Patient|null */
    protected $patient = null;

    /** @var Lab|null */
    protected $lab = null;

    /** @var Date|null */
    private $date;

    /** @var string|null */
    protected $token;

    /** @var Email */
    protected $mail;

    /** @var string */
    protected $mailName;

    protected $mailSubject;

    protected $mailBody;

    protected $mailFooter;

    protected $toAddress;

    protected $toName;

    protected $fromAddress;

    protected $fromName;

    protected $xTags = '';

    protected $toPatient = false;

    protected $toLab = false;

    protected $withDeleteLink = true;

    /**
     * Should log error, rather than sending error email.
     *
     * @var bool
     */
    protected $shouldLogError = false;

    /**
     * Mailer constructor.
     *
     * @param string $mailName
     * @param Patient|null $patient
     * @param Lab|null $lab
     * @param Date|null $date
     * @param string|null $token
     */
    public function __construct($mailName, $patient = null, $lab = null, $date = null, $token = null)
    {
        $this->lab = $lab;
        $this->patient = $patient;
        $this->date = $date;
        $this->token = $token;

        $this->initializeMail($mailName);
    }

    /**
     * Initialize mail.
     */
    public function initializeMail($mailName)
    {
        $email = $this->getMail($mailName);

        $this->mail = $email;
        $this->mailName = $mailName;

        if (get_class($this->patient) === 'App\Patient') {
            $this->mailSubject = strip_tags(Helper::markdown($email->subject, $this->lab, $this->patient, $this->date, $this->token));
            $this->mailBody = Helper::markdown($email->body, $this->lab, $this->patient, $this->date, $this->token);
            $this->mailFooter = Helper::markdown($email->footer, $this->lab, $this->patient, $this->date, $this->token);
        }

        if (get_class($this->patient) === 'App\DentistContact') {
            $this->mailSubject = strip_tags(Helper::markdowndentist($email->subject, $this->lab, $this->patient, $this->date, $this->token));
            $this->mailBody = Helper::markdowndentist($email->body, $this->lab, $this->patient, $this->date, $this->token);
            $this->mailFooter = Helper::markdowndentist($email->footer, $this->lab, $this->patient, $this->date, $this->token);
        }


        $this->fromAddress = config('mail.from.address');
        $this->fromName = config('mail.from.name');
    }

    /**
     * Set receiver.
     *
     * @param $address
     * @param $name
     * @return $this
     */
    public function to($address, $name, $toPatient = false)
    {
        $this->toAddress = $address;
        $this->toName = $name;

        $this->toPatient = $toPatient;

        return $this;
    }

    /**
     * Send mail to patient.
     *
     * @return $this
     */
    public function toPatient()
    {
        if (is_object($this->patient)) {
            if (is_object($this->patient->patientmeta)) {
                if ($this->patient->patientmeta->email) {
                    $comparsed_email = str_replace('..', '.',  $this->patient->patientmeta->email);
                    $comparsed_email = str_replace(' ', '', $comparsed_email);
                    $this->toAddress = trim($comparsed_email);
                    $this->toName = $this->patient->patientmeta->name;

                    $this->toPatient = true;
                }
            }
        }
        return $this;
    }

    /**
     * Send mail to lab.
     *
     * @return $this
     */
    public function toLab()
    {
        $this->toAddress = $this->lab->user->email;
        $this->toName = $this->lab->labmeta->contact_person;

        $this->toLab = true;

        return $this;
    }

    /**
     * Set sender.
     *
     * @param $address
     * @param null $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        $this->fromAddress = $address;

        if ($name) {
            $this->fromName = $name;
        }

        return $this;
    }

    public function fromSecondary()
    {
        $this->fromAddress = config('mail.from.secondary_address');

        return $this;
    }

    /**
     * Set X-Tags.
     *
     * @param $tags
     * @return $this
     */
    public function xtags($tags)
    {
        $this->xTags = $tags;

        return $this;
    }

    public function withoutDeleteLink()
    {
        $this->withDeleteLink = false;

        return $this;
    }

    /**
     * Send email.
     */
    public function send()
    {
        $this->checkEnvironment();

        try {
            $this->mailFooter .= mailSignature();

            if ($this->withDeleteLink && get_class($this->patient) === 'App\Patient') {
                $this->mailFooter .= dsgvoMessage();
            }

            Mail::send('emails.default-mail', [
                'body'   => $this->mailBody,
                'footer' => $this->mailFooter,
            ], function ($message) {
                $message->to($this->toAddress, $this->toName)->subject($this->mailSubject);

                $message->from($this->fromAddress, $this->fromName);

                $message->getHeaders()->addTextHeader('X-MC-Tags', $this->xTags);
            });

            // Log an activity, if sent to patient.
            if ($this->toPatient) {
                activity()->causedBy(adminUser())->performedOn($this->patient)->withProperties(['subject' => $this->mailSubject, 'causedBySystem' => true, 'mail' => $this->mail])->log('mail_sent');
            }

            // Log an activity, if sent to Lab.
            if ($this->toLab) {
                activity()->causedBy(adminUser())->performedOn($this->lab)->withProperties(['subject' => $this->mailSubject, 'causedBySystem' => true, 'mail' => $this->mail])->log('mail_sent');

                if ($this->patient) {
                    activity()->causedBy(adminUser())->performedOn($this->patient)->withProperties(['subject' => $this->mailSubject, 'causedBySystem' => true, 'mail' => $this->mail, 'receiver' => $this->lab->name])->log('mail_sent');
                }
            }

        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }

    /**
     * Should log error, rather than sending error email.
     *
     * @return $this
     */
    public function shouldLogError()
    {
        $this->shouldLogError = true;

        return $this;
    }

    /**
     * Get email settings.
     *
     * @param $name
     * @return Email|null
     */
    public function getMail($name)
    {
        return Email::where('name', $name)->first();
    }

    /**
     * Set development email address and x-tags, if environment is local.
     */
    private function checkEnvironment()
    {
        if (app()->environment('local')) {
            $this->toAddress = getDevMail();
            $this->xTags = "Test, {$this->xTags}";
        }
    }

    /**
     * Log error, generated while sending mail.
     */
    private function logError($message)
    {
        $msg = "[MailError] => Mail  konnte nicht verschickt werden.";
        if ($this->patient) {
            if ($this->patient->patientmeta) {
                $msg = sprintf('[MailError] => Mail an "%s" <%s> [PLZ: %s] konnte nicht verschickt werden.',
                    $this->patient->patientmeta->name,
                    $this->patient->patientmeta->email,
                    $this->patient->patientmeta->zip
                );
            }
        }

        Log::error($msg . ' [Exception: ' . $message . ']');

        /*if ($this->shouldLogError) {
            $msg = "Leider gab es einen Fehler mit {$this->toAddress}";

            Mail::send('emails.system-mail', ['msg' => $msg], function ($message) {
                $message->to(getDevMail(), 'Marc')->subject('[Padento] Mails gehen nicht raus!');

                $message->from($this->fromAddress, $this->fromName);

                $message->getHeaders()->addTextHeader('X-MC-Tags', 'Fehler, Systemmail, Mails');
            });
        } else {
            $msg = sprintf('[MailError] => Mail an "%s" <%s> [PLZ: %s] konnte nicht verschickt werden.',
                $this->patient->patientmeta->name,
                $this->patient->patientmeta->email,
                $this->patient->patientmeta->zip
            );

            Log::info($msg);
//            Activity::log($msg);
        }*/
    }
}