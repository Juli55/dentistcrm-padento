<?php

namespace App\Console\Commands;

use Event;
use App\Patient;
use App\Events\PatientDeleted;
use Illuminate\Console\Command;

class DeleteArchivedContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:archived-contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete archived contacts with phase "no interest"';

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
        $patients = Patient::onlyTrashed()
            ->orWhereNull('lab_id')
            ->orWhere(function ($q) {
                $q->where('phase', 6)->where('archived', true);
            })
            ->with('patientmeta')
            ->get();

        foreach ($patients as $patient) {
//            if ($patient->patientmeta) {
//                Event::fire(new PatientDeleted($patient));
//            }

            $patient->forceDelete();
        }

        logger('Archived contacts deleted: ' . $patients->count());
    }
}
