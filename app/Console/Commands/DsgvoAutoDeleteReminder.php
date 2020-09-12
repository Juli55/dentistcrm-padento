<?php

namespace App\Console\Commands;

use App\Activity;
use App\Lab;
use App\Patient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class DsgvoAutoDeleteReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsgvo:auto-delete-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DSGVO auto delete reminder email';

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
        $patientIds = Activity::where('subject_type', Patient::class)
            ->where('created_at', '>', Carbon::today()->subMonths(11))
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        $patients = Patient::whereNotIn('id', $patientIds)
            ->where('created_at', '<', Carbon::today()->subMonths(11))
            ->whereNull('estimated_deletation_at')
            ->get();

        foreach ($patients as $patient) {
            $patient->estimated_deletation_at = Carbon::today()->addDays(30);
            $patient->save();

            activity()->performedOn($patient)->log('mailed_dsgvo');

            mailer('DSGVOAutoDeleteReminder', $patient)->withoutDeleteLink()->toPatient()->send();
        }

        logger('DSGVO reminder: ' . $patients->count());
    }
}
