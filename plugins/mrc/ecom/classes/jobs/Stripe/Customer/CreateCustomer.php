<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Customer;

use Log;
use Mrc\Ecom\Services\Stripe;

class CreateCustomer
{
    public function fire($job, $data = null)
    {
        Log::info('Create Customer on Stripe');
        $stripe = new Stripe;
        $stripe->createCustomer($data);
        $job->delete();
    }
}
