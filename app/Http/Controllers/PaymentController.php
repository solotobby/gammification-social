<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FlutterwavePaymentService;
use App\Services\KorapayService;
use App\Services\SubscriptionService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    protected SubscriptionService $subscriptionService;
    protected FlutterwavePaymentService $flutterwavePaymentService;
    protected TransactionService $transactionService;
    protected KorapayService $korapayService;

    public function __construct(SubscriptionService $subscriptionService, FlutterwavePaymentService $flutterwavePaymentService, TransactionService $transactionService, KorapayService $korapayService)
    {
        $this->middleware('auth');
        $this->subscriptionService = $subscriptionService;
        $this->flutterwavePaymentService = $flutterwavePaymentService;
        $this->transactionService = $transactionService;
        $this->korapayService = $korapayService;
    }

    public function createSubscription($levelId, SubscriptionService $subscriptionService, FlutterwavePaymentService $flutterwavePaymentService, KorapayService $korapayService)
    {

        //subscription payment flow

        $user = auth()->user();
        $userCurrency = userBaseCurrency(); //$user->wallet->currency;

        // if ($userCurrency == 'NGN') {


        //     $authUrl = $korapayService->initiatePayment($levelId);
        //     //$subscriptionService->processSubscriptionPayment($levelId);

        //     return redirect($authUrl);
        // } else {

        //fetch payment plan based on level and currency
        $userpaymentPlan = $flutterwavePaymentService->getPaymentPlan($user, $levelId, $userCurrency);

        if ($userpaymentPlan) {
            $paymentPlan = $userpaymentPlan;
        } else {
            $paymentPlan = $flutterwavePaymentService->createPaymentPlan($levelId);
        }

        // return $paymentPlan;

        $charge = $flutterwavePaymentService->createCharge($levelId, $paymentPlan);

        return redirect($charge);
        // }
    }

    public function createPaygSubscription($levelId, SubscriptionService $subscriptionService, FlutterwavePaymentService $flutterwavePaymentService, KorapayService $korapayService)
    {

        $user = auth()->user();
        // $userCurrency = $user->wallet->currency;

        $authUrl = $korapayService->initiatePayment($levelId);
        //$subscriptionService->processSubscriptionPayment($levelId);

        return redirect($authUrl);


        // if ($userCurrency == 'NGN') {

        //     $authUrl = $korapayService->initiatePaygPayment($levelId);
        //     //$subscriptionService->processSubscriptionPayment($levelId);

        //     return redirect($authUrl);
        // } else {

        //     //fetch payment plan based on level and currency
        //     $userpaymentPlan = $flutterwavePaymentService->getPaymentPlan($user, $levelId, $userCurrency);

        //     if ($userpaymentPlan) {
        //         $paymentPlan = $userpaymentPlan;
        //     } else {
        //         $paymentPlan = $flutterwavePaymentService->createPaymentPlan($levelId);
        //     }

        //     // return $paymentPlan;

        //     $charge = $flutterwavePaymentService->createCharge($levelId, $paymentPlan);

        //     return redirect($charge);
        // }       
    }

    public function verifyKoraSubscriptionPayment(SubscriptionService $subscriptionService, KorapayService $korapayService)
    {

        try {
            $result = $korapayService->verifySubscriptionPayment(
                $reference = request()->query('reference')
            );

            return redirect('upgrade')->with(
                'success',
                "Payment received successfully. Account upgrade processing..."
            );
        } catch (\Exception $e) {
            return redirect('upgrade')->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function verifyFlutterwaveCharge(Request $request, FlutterwavePaymentService $flutterwavePaymentService, TransactionService $transactionService)
    {

        $txRef = $request->query('tx_ref');
        $status = $request->query('status');
        $tx_id = $request->query('transaction_id');


        if (!$txRef && !$status && !$tx_id) {
            abort(400, 'Invalid transaction reference and id.');
        }



        if ($status == 'cancelled') {
            $data['reference'] = $txRef;
            $data['transaction_id'] = $tx_id;
            $data['status'] = $status;

            $transaction = Transaction::where('ref', $txRef)->lockForUpdate()
                ->first();
            $transactionService->markCancelled($transaction, $data);

            return redirect('upgrade')->with(
                'error',
                "Payment Cancelled by  " . auth()->user()->name .
                    " Therefore payment not successful"
            );
        }

        if ($status == 'successful' || $status == 'completed') {

            try {

                $verifyFlutterwavePayment = $flutterwavePaymentService->verifyTransaction($txRef);

                if ($verifyFlutterwavePayment['status'] === 'successful') {
                    return redirect('upgrade')->with(
                        'success',
                        "Payment Recieved, account upgrade processing..."
                        // "Next payment: {$result['next_payment_date']->toDateString()}"
                    );
                }
            } catch (\Exception $e) {
                return redirect('upgrade')->with(
                    'error',
                    $e->getMessage()
                );
            }
        }
    }
}
