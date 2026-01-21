<?php

namespace App\Console;

use App\Mail\GeneralMail;
use App\Models\Level;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // $schedule->command('engagement:daily')
        //     ->dailyAt('00:04')
        //     ->withoutOverlapping()
        //     ->onOneServer()->runInBackground();

        // $schedule->command('engagement:monthly')
        //     ->monthlyOn(1, '01:00') // Run on 1st of every month at 01:00 AM
        //     ->withoutOverlapping()
        //     ->onOneServer()->runInBackground();

        // $schedule->command('subscriptions:deactivate-expired')
        //     ->everyMinute();
        // ->dailyAt('00:55');          // 5 minutes after midnight
        // ->withoutOverlapping()
        // ->onOneServer()
        // ->runInBackground();


        //deactivate expiered subscriptions notification mail
        $schedule->call(function () {


            $today = Carbon::today();


            $level = Level::where('name', 'Basic')->first();


            // DB::transaction(function () use ($today, $level) {


                $expired = UserLevel::where('status', 'active')
                    ->whereDate('next_payment_date', '<', $today)
                    ->lockForUpdate()
                    ->get();

                // if ($expired->isEmpty()) {
                //     //$this->info('No expired subscriptions found.');
                //     return;
                // }

                // foreach ($expired as $sub) {
                //     $sub->update([
                //         'level_id' => $level->id, // Downgrade to Basic level
                //         'plan_name' => 'Basic',
                //         'status'     => 'active',
                //         'start_date'   => now(),
                //         'next_payment_date' => now()->addYear() // safer than 30 days
                //     ]);
                // }

                $subject = 'Deactivated Subscription';
                $content = "Deactivated {$expired->count()} subscriptions today";

                Mail::to('solotob3@gmail.com')
                    ->send(new GeneralMail(
                        (object)[
                            'name' => 'Daniel',
                            'email' => 'solotob3@gmail.com'
                        ],
                        $subject,
                        $content
                    ));
            // });

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
