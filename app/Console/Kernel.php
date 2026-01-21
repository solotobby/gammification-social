<?php

namespace App\Console;

use App\Mail\GeneralMail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->command('engagement:daily')
            ->dailyAt('00:04')
            ->withoutOverlapping()
            ->onOneServer()->runInBackground();

        $schedule->command('engagement:monthly')
            ->monthlyOn(1, '01:00') // Run on 1st of every month at 01:00 AM
            ->withoutOverlapping()
            ->onOneServer()->runInBackground();

        $schedule->command('subscriptions:deactivate-expired')
            ->everyMinute();
        // ->dailyAt('00:55');          // 5 minutes after midnight
        // ->withoutOverlapping()
        // ->onOneServer()
        // ->runInBackground();

         $schedule->call(function () {
             $subject = 'Deactivated Subscription';
            $content = "Deactivated {8} subscriptions today";

            
            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Daniel',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));

            })->everyMinute();


    }

    protected $commands = [
        Commands\DailyEngagementStat::class,
        Commands\MonthlyEngagementStat::class,
        Commands\DeactivateExpiredSubscriptions::class
    ];

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
