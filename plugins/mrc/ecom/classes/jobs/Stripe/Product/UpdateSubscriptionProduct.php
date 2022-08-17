<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Product;

use Log;
use Mrc\Ecom\Services\Stripe;

class UpdateSubscriptionProduct
{
    public function fire($job, $data = null)
    {
        Log::info('Update Subscription Product on Stripe');
        $stripe = new Stripe;
        $stripe->updateSubscriptionProduct($data);
        $job->delete();
    }
}
