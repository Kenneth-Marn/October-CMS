<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Coupon;

use Log;
use Mrc\Ecom\Models\Subscription;
use Mrc\Ecom\Services\Stripe;

class CreateCoupon
{
    public function fire($job, $data = null)
    {
        Log::info('Create Coupon on Stripe');
        $stripe = new Stripe;
        
        $coupon = $stripe->createCoupon($data);
        $job->delete();
    }
}