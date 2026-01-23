<?php

namespace App\Console;

use App\Mail\GeneralMail;
use App\Models\EngagementDailyStat;
use App\Models\EngagementMonthlyStat;
use App\Models\FremiumEngagementStat;
use App\Models\Level;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
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


        



        //Monthly Engagement Stats
        $schedule->call(function () {

            $month = now()->subMonth()->format('Y-m');

            // $this->info('Fetching Daily Engagement stat');
            $stats = EngagementDailyStat::whereBetween(
                'date',
                [
                    Carbon::createFromFormat('Y-m', $month)->startOfMonth(),
                    Carbon::createFromFormat('Y-m', $month)->endOfMonth(),
                ]
            )->groupBy('user_id', 'level')
                ->selectRaw('
                user_id,
                level,
                SUM(views) as views,
                SUM(likes) as likes,
                SUM(comments) as comments,
                SUM(points) as points
            ')->get();


            foreach ($stats as $stat) {

                EngagementMonthlyStat::updateOrCreate(
                    [
                        'user_id' => $stat->user_id,
                        'level'    => $stat->level,
                        'month'   => $month,
                    ],
                    [
                        'views'    => $stat->views,
                        'likes'    => $stat->likes,
                        'comments' => $stat->comments,
                        'points'   => $stat->points,
                    ]
                );
            }

            $subject = 'Monthly Engagement Registered';
            $content = "Registered Monthly Stats successfully";
            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));
        })->monthlyOn(1, '01:00');
       


        //Daily Engagement Stats
        $schedule->call(function () {


            $date = now()->subDay()->toDateString(); // yesterday

            $activeUsers = UserLevel::where('status', 'active')
                ->whereIn('plan_name', ['Creator', 'Influencer'])
                ->with('user:id')
                ->get();

            // $this->info('Calculating and registring daily stat');
            foreach ($activeUsers as $userLevel) {


                DB::transaction(function () use ($userLevel, $date) {
                    // Skip if already calculated
                    if (EngagementDailyStat::where('user_id', $userLevel->user_id)
                        ->where('date', $date)
                        ->exists()
                    ) {
                        return;
                    }


                    $views = UserView::where('poster_user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $likes = UserLike::where('poster_user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $comments = UserComment::where('poster_user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $points = $views + $likes + $comments;

                    if ($points === 0) {
                        return;
                    }

                    EngagementDailyStat::create([
                        'user_id'  => $userLevel->user_id,
                        'level'     => $userLevel->plan_name,
                        'date'     => $date,
                        'views'    => $views,
                        'likes'    => $likes,
                        'comments' => $comments,
                        'points'   => $points,
                    ]);
                });
            }

            $subject = 'Daily Engagement Registered';
            $content = "Registered Daily Stats successfully";


            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));
        })->dailyAt('00:14');
     


        //deactivate expiered subscriptions notification mail
        $schedule->call(function () {


            $today = Carbon::today();


            $level = Level::where('name', 'Basic')->first();


            DB::transaction(function () use ($today, $level) {


                $expired = UserLevel::where('status', 'active')
                    ->whereDate('next_payment_date', '<', $today)
                    ->lockForUpdate()
                    ->get();

                if ($expired->isEmpty()) {
                    //$this->info('No expired subscriptions found.');
                    return;
                }

                foreach ($expired as $sub) {
                    $sub->update([
                        'level_id' => $level->id, // Downgrade to Basic level
                        'plan_name' => 'Basic',
                        'status'     => 'active',
                        'start_date'   => now(),
                        'next_payment_date' => now()->addYear() // safer than 30 days
                    ]);
                }

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
            });
        })->dailyAt('11:50');
        

         //Freemium Daily Engagement Stats
        $schedule->call(function () {

            $date = now()->subDay()->toDateString(); // yesterday

            $activeUsers = UserLevel::where('status', 'active')
                // ->whereIn('plan_name', ['Creator', 'Influencer'])
                ->with('user:id')
                ->get();

            // $this->info('Calculating and registring daily stat');
            foreach ($activeUsers as $userLevel) {


                DB::transaction(function () use ($userLevel, $date) {
                    // Skip if already calculated
                    if (EngagementDailyStat::where('user_id', $userLevel->user_id)
                        ->where('date', $date)
                        ->exists()
                    ) {
                        return;
                    }


                    $views = UserView::where('user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $likes = UserLike::where('user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $comments = UserComment::where('user_id', $userLevel->user_id)
                        ->whereDate('created_at', $date)
                        ->count();

                    $points = $views + $likes + $comments;

                    if ($points === 0) {
                        return;
                    }

                    FremiumEngagementStat::create([
                        'user_id'  => $userLevel->user_id,
                        'level'     => $userLevel->plan_name,
                        'date'     => $date,
                        'views'    => $views,
                        'likes'    => $likes,
                        'comments' => $comments,
                        'points'   => $points,
                    ]);
                });
            }

            $subject = 'Freemium Daily Engagement Registered';
            $content = "Registered Freemium Daily Stats successfully";


            Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));
        })->dailyAt('00:40');

        $schedule->call(function () {

            $subject = 'Daily Engagement Registered';
            $content = "Registered Daily Stats successfully";


             Mail::to('solotob3@gmail.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Oluwatobi Solomon',
                        'email' => 'solotob3@gmail.com'
                    ],
                    $subject,
                    $content
                ));
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
