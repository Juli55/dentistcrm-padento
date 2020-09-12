<?php

use App\Activity;
use App\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RemoveOldContacts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $patientIds = Activity::where('subject_type', Patient::class)
            ->where('created_at', '>', Carbon::today()->subMonths(12))
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        $patients = Patient::whereNotIn('id', $patientIds)->get();

        foreach ($patients as $patient) {
            $patient->forceDelete();
        }
    }
}
