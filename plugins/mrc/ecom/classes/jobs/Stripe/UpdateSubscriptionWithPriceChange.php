<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe;

use Log;
use Mrc\Ecom\Services\Stripe;

class UpdateSubscriptionWithPriceChange
{
    public function fire($job, $data = null)
    {
        Log::info('Update Subscription Product with Price Change on Stripe');
        $stripe = new Stripe;
        $stripe->updateSubscriptionWithPriceChange($data);
        $job->delete();
    }
}
