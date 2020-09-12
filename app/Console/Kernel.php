<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CronDate::class,
        Commands\CronMail::class,
        Commands\CheckAttachments::class,
        Commands\ConvertArchived::class,
        Commands\SendDentistDateMails::class,
        Commands\SMSReminder::class,
        Commands\DsgvoAutoDeleteReminder::class,
        Commands\DsgvoAutoDelete::class,
        Commands\DeleteArchivedContacts::class,
        Commands\DeleteArchivedContacts::class,
        Commands\CleanActivities::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run --only-db')->daily()->at('02:00');

        $schedule->command('cronmail:lastday')->daily()->at('06:00');
        $schedule->command('crondates:nextday')->daily()->at('06:00');
        $schedule->command('mail:dentist-dates')->daily()->at('07:00');

        $schedule->command('dsgvo:auto-delete-reminder')->daily()->at('07:30');
        $schedule->command('dsgvo:auto-delete')->daily()->at('08:00');
//        $schedule->command('delete:archived-contacts')->daily()->at('08:15');

        $schedule->command('sms:date-reminders')->daily()->at('13:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
