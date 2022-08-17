<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Subscription\Webhook;

use Log;
use Mrc\Ecom\Models\Subscription;

class SubscriptionUpdateWebhook
{
    public function fire($job, $event = null)
    {
        $subscriptionObject = (object) $event['data']['object'];
        $stripeSubscriptionId = $subscriptionObject->id;
        Log::info(print_r($subscriptionObject, true));
        
        $payLoad = [
            'start_date' =>  $subscriptionObject->current_period_end ? gmdate("Y-m-d H:i:s", $subscriptionObject->current_period_start) : null,
            'end_date' => $subscriptionObject->current_period_end ? gmdate("Y-m-d H:i:s", $subscriptionObject->current_period_end) : null,
            'cancel_at' => $subscriptionObject->cancel_at ? gmdate("Y-m-d H:i:s", $subscriptionObject->cancel_at) : null,
            'canceled_at' => $subscriptionObject->canceled_at ? gmdate("Y-m-d H:i:s", $subscriptionObject->canceled_at) : null,
        ];
        
        if ($subscriptionObject->cancel_at_period_end) {
            $payLoad['cancel_options'] = 'cancel_at_the_end';
        } elseif ($subscriptionObject->canceled_at) {
            $payLoad['cancel_options'] = 'cancel_immediately';
        }
        
        Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->update($payLoad);
        $job->delete();
    }
}
