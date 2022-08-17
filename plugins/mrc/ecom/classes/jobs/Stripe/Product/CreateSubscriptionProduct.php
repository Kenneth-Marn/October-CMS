<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Product;

use Log;
use Mrc\Ecom\Services\Stripe;

class CreateSubscriptionProduct
{
    public function fire($job, $data = null)
    {
        Log::info('Create Subscription Product on Stripe');
        $stripe = new Stripe;
        $stripe->createSubscriptionProduct($data);
        $job->delete();
    }
}
