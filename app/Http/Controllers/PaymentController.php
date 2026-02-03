<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->middleware('auth');
        $this->subscriptionService = $subscriptionService;
    }

    public function createSubscription($levelId, SubscriptionService $subscriptionService)
    {

        $authUrl = $subscriptionService->processSubscriptionPayment($levelId);

        return redirect($authUrl);
    }

    public function verifyKoraSubscriptionPayment(SubscriptionService $subscriptionService)
    {

        try {
            $result = $subscriptionService->verifySubscriptionPayment(
                $reference = request()->query('reference')
            );

            return redirect('upgrade')->with(
                'success',
                "Successfully upgraded to {$result['level_name']}. " .
                    "Next payment: {$result['next_payment_date']->toDateString()}"
            );
        } catch (\Exception $e) {
            return redirect('upgrade')->with(
                'error',
                $e->getMessage()
            );
        }

    }
}
