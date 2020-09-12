<?php

namespace App\Console\Commands;

use App\Date;
use Illuminate\Console\Command;

class DateReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:datereminder';

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

        $dates = Date::whereDate('date', '=', Carbon::now()->tomorrow());

          foreach( $users as $user ) {
            if($user->has('cellphone')) {
            SMS::to($user->cellphone)
               ->msg('Dear ' . $user->fname . ', I wish you a happy birthday!')
               ->send();
            }
        }

        $this->info('The happy birthday messages were sent successfully!');
    }
}
