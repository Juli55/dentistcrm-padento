<?php

namespace App\Console\Commands;

use App\Date;
use App\DentistContact;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDentistDateMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:dentist-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send dentist date mails';

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
        $dates = Date::has('dentist_contact')->whereBetween('date', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
            ->with('dentist_contact', 'lab')
            ->get();

        foreach ($dates as $date) {
            /* mailer('Zahnarzt-Labormail', $date->dentist_contact, $date->lab, $date)
                ->toLab()
                ->fromSecondary()
                ->xtags('Labor, Termin')
                ->send(); */
        }
    }
}
