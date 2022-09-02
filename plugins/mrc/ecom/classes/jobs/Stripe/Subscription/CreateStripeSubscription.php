<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Subscription;

use Exception;
use Log;
use Mrc\Ecom\Models\Product;
use Mrc\Ecom\Models\Subscription;
use Mrc\Ecom\Services\Stripe;
use RainLab\User\Models\User;

class CreateStripeSubscription
{
    public function fire($job, $data = null)
    {
        Log::info('Create Subscription on Stripe');
        $stripe = new Stripe;
        
        $subscriptionSchedule = $stripe->createSubscription($data);
        $job->delete();
    }
}
