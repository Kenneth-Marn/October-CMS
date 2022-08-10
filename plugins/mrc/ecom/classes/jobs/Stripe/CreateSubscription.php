<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe;

use Log;
use Mrc\Ecom\Models\Subscription;
use Mrc\Ecom\Services\Stripe;

class CreateSubscription
{
    public function fire($job, $data = null)
    {
        Log::info('Create Customer on Stripe');
        $stripe = new Stripe;
    
        if ($data['user']['stripe_customer_id']) {
            $stripe->addSourceCustomer($data['user'], $data['stripeToken']);
        } else {
            $stripe->createCustomer($data['user']);
            $stripe->addSourceCustomer($data['user'], $data['stripeToken']);
        }
        
        $subscription = $stripe->createSubscription($data['user'], $data['product']);
        
        Subscription::create([
            'user_id' => $data['user']['id'],
            'product_id' => $data['product']['id'],
            'start_date' =>  gmdate("Y-m-d H:i:s", $subscription->current_period_start),
            'end_date' => gmdate("Y-m-d H:i:s", $subscription->current_period_end),
            'stripe_subscription_id' => $subscription->id
        ]);
        
        $job->delete();
    }
}