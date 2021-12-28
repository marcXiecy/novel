<?php

namespace App\Console;

use App\Models\wxUser;
use App\Schedules\RefreshBook;
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

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(new RefreshBook)->everyFourHours();
        // $schedule->command('stock:jisilu-kezhuanzhai')->everyMinute();
        $schedule->command('stock:jisilu-kezhuanzhai')->weekdays()->at('12:00');
        // $schedule->command('stock:jisilu-kezhuanzhai')->dailyAt('11:53');
        // $schedule->command('stock:jisilu-kezhuanzhai')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
