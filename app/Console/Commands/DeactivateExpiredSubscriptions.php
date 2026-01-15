<?php

namespace App\Console\Commands;

use App\Mail\GeneralMail;
use App\Models\Level;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DeactivateExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:deactivate-expired';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate active subscriptions whose next payment date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $today = Carbon::today();

        $this->info('Checking for expired subscriptions...');
        $level = Level::where('name', 'Basic')->first();

        DB::transaction(function () use ($today, $level) {

            $expired = UserLevel::where('status', 'active')
                ->whereDate('next_payment_date', '<', $today)
                ->lockForUpdate()
                ->get();

            if ($expired->isEmpty()) {
                $this->info('No expired subscriptions found.');
                return;
            }

            foreach ($expired as $sub) {
                $sub->update([
                    'level_id' => $level->id,
                    'plan_name' => 'Basic',
                    'status'     => 'active',
                    'start_date'   => now(),
                    'next_payment_date' => now()->addYear() // safer than 30 days
                ]);
            }

            $this->info("Deactivated {$expired->count()} subscriptions.");

            $subject = 'Deactivated Sunscriober';
            $content = "Deactivated {$expired->count()} subscriptions today";

            Mail::to('oluwatobi@freebyztechnologies.com')
                ->send(new GeneralMail(
                    (object)[
                        'name' => 'Daniel',
                        'email' => 'oluwatobi@freebyztechnologies.com'
                    ],
                    $subject,
                    $content
                ));
        });




        return Command::SUCCESS;
    }
}
