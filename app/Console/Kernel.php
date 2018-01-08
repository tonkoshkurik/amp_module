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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
      $schedule->command('amo:push')->everyMinute()->when(function () {
        $inleads = \App\Lead::whereNull('status')->whereNull('payed')->get();
        return $inleads->count() ? true : false;
      });

      $schedule->command('amo:update')->everyMinute()->when(function () {
        $payedleads = \App\Lead::whereNull('status')->whereNotNull('payed')->get();
        return $payedleads->count() ? true : false;
      });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
