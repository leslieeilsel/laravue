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
        'App\Console\Commands\buildDingProjectJson',
        'App\Console\Commands\buildProjectGeoJson',
        'App\Console\Commands\buildLuwangGeoJson',
        'App\Console\Commands\buildDataVLuWangJsonData',
        'App\Console\Commands\buildLvHuaJson',
        'App\Console\Commands\buildCache',
        'App\Console\Commands\RefreshProjectUnit',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('buildProjectGeoJson --force')->everyMinute();
//        $schedule->command('buildDingProjectJson --force')->everyMinute();
        // $schedule->command('inspire')
        //          ->hourly();
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
