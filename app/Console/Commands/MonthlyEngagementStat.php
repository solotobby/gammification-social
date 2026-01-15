<?php

namespace App\Console\Commands;

use App\Models\EngagementDailyStat;
use App\Models\EngagementMonthlyStat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyEngagementStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engagement:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly  description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = now()->subMonth()->format('Y-m');

        $this->info('Fetching Daily Engagement stat');
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

        $this->info('Registring Monthlyu Engagement stat');
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

        return Command::SUCCESS;
    }
}
