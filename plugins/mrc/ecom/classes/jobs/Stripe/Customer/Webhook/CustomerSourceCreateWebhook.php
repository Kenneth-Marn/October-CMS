<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Customer\Webhook;

use RainLab\User\Models\User;

class CustomerSourceCreateWebhook
{
    public function fire($job, $event = null)
    {
        $source = (object) $event['data']['object'];
        User::where(['stripe_customer_id' => $source->customer])->update([
            'stripe_card_id' => $source->id
        ]);

        $job->delete();
    }
}