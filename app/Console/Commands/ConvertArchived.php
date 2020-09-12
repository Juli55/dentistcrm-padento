<?php

namespace App\Console\Commands;

use App\Patient;
use Illuminate\Console\Command;

class ConvertArchived extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertarchived:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'convert all patients that are alreadyy not interested to archived Status';

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
        $patints =Patient::where('phase',6)->get();

        foreach($patints as $patint){
            $patint->archived=1;
            $patint->save();
        }
    }
}
