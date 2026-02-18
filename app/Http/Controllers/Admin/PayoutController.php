<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\EngagementMonthlyStat;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Notifications\GeneralNotification;
use App\Services\FundTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PayoutController extends Controller
{

    public $fundTransferService;

    public function __construct(FundTransferService $fundTransferService)
    {
        $this->middleware('auth');
        // $this->middleware('admin');
        $this->fundTransferService = $fundTransferService;
    }   
    public function index($level)
    {

        if (! $level) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => 'Invalid payout level selected.',
            ]);
        }

        $lastMonth = now()->subMonth()->format('Y-m');

        // Freemium users
        if (! in_array($level, ['Influencer', 'Creator'])) {
            return view('admin.payouts.index', [
                'status' => 'skipped',
                'message' => 'Freemium users do not receive payouts.',
                'level' => $level,
                'lastmonth' => $lastMonth,
            ]);
        }

        // Premium processing
        $payouts = $this->processPremium($level, $lastMonth);

        // ❌ Error from service
        if (isset($payouts['error'])) {
            return view('admin.payouts.index', [
                'status' => 'error',
                'message' => $payouts['error'],
                'level' => $level,
                'lastmonth' => $lastMonth,
            ]);
        }

        // ✅ SUCCESS
        return view('admin.payouts.index', [
            'status'           => 'success',
            'payouts'          => $payouts['userEngagement'],
            'totalEngagement'  => $payouts['totalEngagement'],
            'revenue'          => $payouts['revenue'],
            'levelPool'        => $payouts['levelPool'],
            'memberCount'      => $payouts['memberCount'],
            'level'            => $level,
            'lastmonth'        => $lastMonth,
        ]);
    }

    public function queuePayout($id)
    {
        $engagementStat = EngagementMonthlyStat::find($id);
        // $fetchWallet = Wallet::where('user_id', $engagementStat->user_id)->first();

        $payout = Payout::create([
            'engagement_monthly_stats_id' => $id,
            'user_id' => $engagementStat->user_id,
            'level' => $engagementStat->level,
            'amount' => convertToBaseCurrency($engagementStat->amount, 'NGN'),
            'total_engagement' => $engagementStat->points,
            'month' => $engagementStat->month,
            'currency' => $engagementStat->currency ?? 'NGN',
            'status' => 'Queued',
            'type' => 'Premium'
        ]);

        if ($payout) {

            $engagementStat->update(['status' => 'Queued']);

            $userEmail =  $engagementStat->user->email;
            $userName =  $engagementStat->user->name;
            $amount = number_format($payout->amount, 2);
            $duration = \Carbon\Carbon::createFromFormat('Y-m', $payout->month)->format('F Y');
            $currency = $payout->currency ?? 'NGN';

            $subject = '🎉 Your Payhankey payout has been processed!';

            $content = "
                        <p>
                            Great news! We’re excited to let you know that your
                            <strong>Payhankey payout has been successfully processed!</strong>.
                        </p>

                        <p>
                            💰 <strong>Payout Amount:</strong> NGN $amount <br>
                            📅 <strong>Period Covered:</strong> $duration
                        </p>

                        <p>
                            This payout reflects your engagement and performance on Payhankey during
                            the selected period. Thank you for creating, engaging, and being an
                            important part of our community — <strong>your efforts truly matter</strong>.
                        </p>

                        <p>
                            If you have any questions about your payout or need assistance, our support
                            team is always here for you.
                        </p>

                        <p>
                            Keep creating. Keep growing.<br>
                            We’re rooting for you 🚀
                        </p>
        ";


            $engagementStat->user->notify(
                (new GeneralNotification([
                    'title'   => '🚀 Payhankey Payout Processed!!',
                    'message' => 'Great news! We’re excited to let you know that your Payhankey payout has been successfully processed!',
                    'icon'    => 'fa-heart text-danger',
                    'url'     => url('wallets'),
                ]))->delay(now()->addSeconds(1))
            );

            Mail::to($userEmail)
                ->send(new GeneralMail(
                    (object)[
                        'name' => $userName,
                        'email' => $userEmail
                    ],
                    $subject,
                    $content
                ));
        }

        return back();
    }

    public function viewPayoutInformation($engagementStatId)
    {

        $res = securityVerification();
        if ($res == 'OK') {
            $payoutInformation = Payout::where('engagement_monthly_stats_id', $engagementStatId)->first();
            $withdrawal = WithdrawalMethod::where('user_id', $payoutInformation->user_id)->first();
            $wallet = Wallet::where('user_id', $payoutInformation->user_id)->first();
            return view('admin.payouts.show', ['payout' => $payoutInformation, 'withdrawals' => $withdrawal, 'wallet' => $wallet]);
        }
    }
    public function updatePayoutStatus($id){
        $payoutInfo = Payout::find($id);
         $payoutInfo->update(['status' => 'Paid']);
         $updatengagment = EngagementMonthlyStat::where('id', $payoutInfo->engagement_monthly_stats_id)->update(['status' => 'Paid']);

         $wallet = Wallet::where('user_id', $payoutInfo->user_id)->first();
        
         if ($wallet->balance > 0) {
            Payout::create([
                'engagement_monthly_stats_id' => $payoutInfo->engagement_monthly_stats_id,
                'user_id' => $payoutInfo->user_id,
                'level' => $payoutInfo->level,
                'amount' => $wallet->balance,
                'total_engagement' => 0.00,
                'month' => $payoutInfo->month,
                'currency' => $fetchWallet->currency ?? 'NGN',
                'status' => 'Queued',
                'type' => 'Bonus'
            ]);

            $wallet->balance = 0.00;
            $wallet->save();
        }

         $payoutInfo->user->notify(
                (new GeneralNotification([
                    'title'   => '🚀 Payhankey Payout Sent!!',
                    'message' => 'Great news! Your Payment has been sent to your account!',
                    'icon'    => 'fa-heart text-danger',
                    'url'     => url('wallets'),
                ]))->delay(now()->addSeconds(1))
            );

             Transaction::create([
                'user_id'    => $payoutInfo->user_id,
                'ref'        => generateTransactionRef(),
                'amount'     => $payoutInfo->amount,
                'currency'   => $payoutInfo->currency,
                'status'     => 'successful',
                'type'       => 'payhankey_payout_and_bonus',
                'action'     => 'Credit',
                'description' => "Payhankey Payout for : " . $payoutInfo->month,
            ]);


            return back()->with('success', 'Payment Updated!');

    }

    


    public function fundTransfer(Request $request)
    {
        // 1️⃣ Security check
        if (securityVerification() !== 'OK') {
            return response()->json(['status' => 'error', 'message' => 'Security verification failed'], 403);
        }



        $withdrawal = WithdrawalMethod::where('user_id', $request->user_id)->first();
        $fundTransferServiceResponse = $this->fundTransferService->transfer(1000, $withdrawal->bank_code, $withdrawal->account_number);

        return $fundTransferServiceResponse;

        // 2️⃣ Validate request
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'validationCode' => 'required|string',
        ]);

        // 3️⃣ Check validation code
        if ($request->validationCode !== env('VALIDATION_CODE')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid validation code'], 422);
        }

        $payoutInfo = Payout::find($request->payout_id);

        if ($payoutInfo->status == 'Paid') {
            return response()->json(['status' => 'error', 'message' => 'Payment Already Processed'], 422);
        }


        // 4️⃣ Fetch withdrawal method
        $withdrawal = WithdrawalMethod::where('user_id', $request->user_id)->first();
        if (!$withdrawal) {
            return response()->json(['status' => 'error', 'message' => 'Withdrawal method not found'], 404);
        }



        //check Wallet balance
        $fetchWallet = Wallet::where('user_id', $request->user_id)->first();
        $walletBalance = $fetchWallet->balance; //convertToBaseCurrency($fetchWallet->balance, $fetchWallet->currency);
        if ($fetchWallet->balance > 0) {
            Payout::create([
                'engagement_monthly_stats_id' => $payoutInfo->engagement_monthly_stats_id,
                'user_id' => $request->user_id,
                'level' => $payoutInfo->level,
                'amount' => $walletBalance,
                'total_engagement' => 0.00,
                'month' => $payoutInfo->month,
                'currency' => $fetchWallet->currency ?? 'NGN',
                'status' => 'Queued',
                'type' => 'Bonus'
            ]);

            $fetchWallet->balance = 0.00;
            $fetchWallet->save();
        }

        // 5️⃣ Perform transfer
        try {

            $amount =  $payoutInfo->amount + $walletBalance;

           // $transferData = $this->transferFund($amount, $withdrawal->recipient_code);

            $payoutInfo->update(['status' => 'Paid']);

            $updatengagment = EngagementMonthlyStat::where('id', $payoutInfo->engagement_monthly_stats_id)->update(['status' => 'Paid']);



            Transaction::create([
                'user_id'    => $request->user_id,
                'ref'        => generateTransactionRef(),
                'amount'     => $amount,
                'currency'   => $fetchWallet->currency,
                'status'     => 'successful',
                'type'       => 'payhankey_payout_and_bonus',
                'action'     => 'Credit',
                'description' => "Payhankey Payout for : " . $payoutInfo->month,
            ]);



            $payoutInfo->user->notify(
                (new GeneralNotification([
                    'title'   => '🚀 Payhankey Payout Sent!!',
                    'message' => 'Great news! Your Payment has been sent to your account!',
                    'icon'    => 'fa-heart text-danger',
                    'url'     => url('wallets'),
                ]))->delay(now()->addSeconds(1))
            );


            $userName = $payoutInfo->user->name;
            $userEmail = $payoutInfo->user->email;

            $amount = number_format($payoutInfo->amount, 2);
            $duration = \Carbon\Carbon::createFromFormat('Y-m', $payoutInfo->month)->format('F Y');
            $currency = $payoutInfo->currency ?? 'NGN';

            $subject = '🎉 Your Payhankey payout has been sent!';

            $content = "
    

                    <p>
                        Fantastic news! Your <strong>Payhankey payout has been successfully processed and sent to your account</strong>.
                    </p>

                    <p>
                        💰 <strong>Payout Amount:</strong> NGN {$amount} <br>
                        📅 <strong>Period Covered:</strong> {$duration}
                    </p>

                    <p>
                        This payout reflects your engagement and performance on Payhankey during the selected period.
                        Thank you for creating, engaging, and being an important part of our community —
                        <strong>your efforts truly matter</strong>.
                    </p>

                    <p>
                        If you have any questions about your payout or need assistance, our support team is always here for you.
                    </p>

                    <p>
                        Keep creating. Keep growing.<br>
                        We’re rooting for you 🚀
                    </p>

                    <p>With love, <br><strong>The Payhankey Team 💜</strong></p>
                ";



            Mail::to($userEmail)
                ->send(new GeneralMail(
                    (object)[
                        'name' => $userName,
                        'email' => $userEmail
                    ],
                    $subject,
                    $content
                ));


            return response()->json([
                'status' => 'success',
                'message' => 'Fund transfer initiated',
                'data' => $transferData
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }


    // private function transferFund($amount, $recipientCode)
    private function transferFund($amount, string $recipientCode): array
    {
        $payload = [
            "source" => "balance",
            "amount" => intval($amount * 100), // Convert to kobo/ng subunit
            "recipient" => $recipientCode,
            "reference" => time() . rand(1000, 9999),
            "reason" => "Payhankey Payout"
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->post('https://api.paystack.co/transfer', $payload)
            ->throw() // Automatically throws if 4xx or 5xx
            ->json();

        return $response['data'] ?? [];
    }

    private function processPremium(string $level, string $lastMonth): array
    {
        $planPrices = [
            'Creator' => 1,
            'Influencer' => 5,
        ];

        // Fetch last month engagement
        $members = EngagementMonthlyStat::with(['user.wallet'])
            ->where('level', $level)
            ->where('month', $lastMonth)
            // ->WHERE('status', 'Pending')
            ->get();

        $memberCount = $members->count();


        if ($memberCount === 0) {
            return [
                'error' => 'No users found.',
            ];
        }

        // Revenue + pool
        $revenue   = $memberCount * $planPrices[$level];
        $levelPool = round($revenue * 0.50, 2);

        $totalEngagement = $members->sum('points');

        $userEngagements = [];

        foreach ($members as $member) {

            // ---- Pro-rata calculations ----
            $percentage = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * 100, 2)
                : 0;

            $payout = $totalEngagement > 0
                ? round(($member->points / $totalEngagement) * $levelPool, 2)
                : 0;

            // ✅ UPDATE payout in DB
            $member->update([
                'amount' => $payout,
            ]);

            $user = $member->user;

            $userEngagements[] = [
                'id'               => $member->id,
                'user_id'          => $member->user_id,
                'name'             => $user->name ?? 'N/A',
                'email'            => $user->email ?? 'N/A',
                'engagement'       => $member->points ?? 0,
                'userPercentage'   => $percentage,
                'userPayout'       => $payout,
                'userWallet'       => $user->wallet->currency ?? 'USD',
                'status'            =>  $member->status
                // 'userPayoutMethod' => $user->withdrawalMethod(),
            ];
        }

        return [
            'userEngagement'  => $userEngagements,
            'totalEngagement' => $totalEngagement,
            'revenue'         => $revenue,
            'memberCount'     => $memberCount,
            'levelPool'       => $levelPool,
        ];
    }
}
