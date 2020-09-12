<?php

namespace App\Console\Commands;

use Event;
use App\Patient;
use Carbon\Carbon;
use App\Events\PatientDeleted;
use Illuminate\Console\Command;

class DsgvoAutoDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsgvo:auto-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DSGVO auto delete contacts';

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
        $patients = Patient::where('estimated_deletation_at', Carbon::today())->with('patientmeta')->get();

        foreach ($patients as $patient) {
            if($patient->patientmeta) {
                Event::fire(new PatientDeleted($patient));
            }

            $patient->forceDelete();
        }

        logger('DSGVO deleted: '. $patients->count());
    }
}
