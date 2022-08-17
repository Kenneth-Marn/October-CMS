<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Subscription;

use Log;
use Mrc\Ecom\Services\Stripe;

class UpdateSubscription
{
    public function fire($job, $data = null)
    {
        Log::info('Update Subscription');
        $stripe = new Stripe;
        $stripe->updateSubscription($data);
        $job->delete();
    }
}
