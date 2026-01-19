<?php

namespace App\Console\Commands;

use App\Mail\GeneralMail;
use App\Models\EngagementDailyStat;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DailyEngagementStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engagement:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Engagement';

    /**
     * Execute the console command.
     */
   
 

    // public function handle()
    // {
    //     $date = now()->subDay()->toDateString(); // yesterday

    //     $this->info('Fetching active subscriptions...');

    //     $activeUsers = UserLevel::where('status', 'active')
    //         ->whereIn('plan_name', ['Creator', 'Influencer'])
    //         ->with('user:id')
    //         ->get();

    //     $this->info('Calculating and registring daily stat');
    //     foreach ($activeUsers as $userLevel) {


    //         DB::transaction(function () use ($userLevel, $date) {

    //             // Skip if already calculated
    //             if (EngagementDailyStat::where('user_id', $userLevel->user_id)
    //                 ->where('date', $date)
    //                 ->exists()
    //             ) {
    //                 return;
    //             }

    //             $views = UserView::where('poster_user_id', $userLevel->user_id)
    //                 ->whereBetween('created_at', [
    //                 now()->startOfMonth(),
    //                 Carbon::parse($date)->endOfDay()
    // ])
    //                 ->count();

    //             $likes = UserLike::where('poster_user_id', $userLevel->user_id)
    //                 ->whereBetween('created_at', [
    //                     now()->startOfMonth(),
    //                     Carbon::parse($date)->endOfDay()
    //                 ])
    //                 ->count();

    //             $comments = UserComment::where('poster_user_id', $userLevel->user_id)
    //                 ->whereBetween('created_at', [
    //                     now()->startOfMonth(),
    //                     Carbon::parse($date)->endOfDay()
    //                 ])
    //                 ->count();

    //             $points = $views + $likes + $comments;

    //             if ($points === 0) {
    //                 return;
    //             }

    //             EngagementDailyStat::create([
    //                 'user_id'  => $userLevel->user_id,
    //                 'level'     => $userLevel->plan_name,
    //                 'date'     => $date,
    //                 'views'    => $views,
    //                 'likes'    => $likes,
    //                 'comments' => $comments,
    //                 'points'   => $points,
    //             ]);
    //         });
    //     }

    //     // $subject = 'Daily Engagement Registered';
    //     // $content = "Registered Daily Stats successfully";


    //     // Mail::to('oluwatobi@freebyztechnologies.com')
    //     //     ->send(new GeneralMail(
    //     //         (object)[
    //     //             'name' => 'Oluwatobi Solomon',
    //     //             'email' => 'oluwatobi@freebyztechnologies.com'
    //     //         ],
    //     //         $subject,
    //     //         $content
    //     //     ));

    //     return Command::SUCCESS;
    // }

    public function handle()
    {
        $date = now()->subDay()->toDateString(); // yesterday

        $this->info('Fetching active subscriptions...');

        $activeUsers = UserLevel::where('status', 'active')
            ->whereIn('plan_name', ['Creator', 'Influencer'])
            ->with('user:id')
            ->get();

        $this->info('Calculating and registring daily stat');
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

        // $subject = 'Daily Engagement Registered';
        // $content = "Registered Daily Stats successfully";


        // Mail::to('oluwatobi@freebyztechnologies.com')
        //     ->send(new GeneralMail(
        //         (object)[
        //             'name' => 'Oluwatobi Solomon',
        //             'email' => 'oluwatobi@freebyztechnologies.com'
        //         ],
        //         $subject,
        //         $content
        //     ));

        return Command::SUCCESS;
    }
}
