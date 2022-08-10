<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe;

use Log;
use Mrc\Ecom\Models\Subscription;
use Mrc\Ecom\Services\Stripe;

class CreateCoupon
{
    public function fire($job, $data = null)
    {
        Log::info('Create Coupon on Stripe');
        $stripe = new Stripe;
        
        $subscription = $stripe->createCoupon($data);
        $job->delete();
    }
}