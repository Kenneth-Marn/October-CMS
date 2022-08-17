<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Customer\Webhook;

use Log;
use Mrc\Ecom\Services\Stripe;

class CreateCustomer
{
    public function fire($job, $data = null)
    {
        Log::info('Create Customer on Stripe');

        $job->delete();
    }
}
