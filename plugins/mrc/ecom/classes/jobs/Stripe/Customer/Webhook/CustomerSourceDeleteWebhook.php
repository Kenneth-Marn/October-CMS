<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Customer\Webhook;

use RainLab\User\Models\User;
use Log;
use Mrc\Ecom\Models\Invoice;
use Mrc\Ecom\Models\Subscription;


class CustomerSourceDeleteWebhook
{
    public function fire($job, $event = null)
    {
        $source = (object) $event['data']['object'];
        $user = User::where('stripe_customer_id', $source->customer)->first();
        $user->stripe_card_id = null;
        $job->delete();
    }
}