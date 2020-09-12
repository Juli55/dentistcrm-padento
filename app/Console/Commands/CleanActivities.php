<?php

namespace App\Console\Commands;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class CleanActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Activities from unnecessary activities';

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
        $activities = Activity::where('description', 'mailed_dsgvo')
            ->get();

            foreach ($activities as $activitie) {
                $activitie->forceDelete();
            }
    }
}
