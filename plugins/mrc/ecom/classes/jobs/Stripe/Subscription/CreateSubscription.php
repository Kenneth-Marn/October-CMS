<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Subscription;

use Mrc\Ecom\Services\Stripe;

class CreateSubscription
{
    public function fire($job, $data = null)
    {
        $stripe = new Stripe;
        
        if ($data['user']['stripe_customer_id']) {
            $stripe->addSourceCustomer($data['user'], $data['data']['stripeToken']);
        } else {
            $stripe->createCustomer($data['user']);
            $stripe->addSourceCustomer($data['user'], $data['data']['stripeToken']);
        }
        
        $job->delete();
    }
}
