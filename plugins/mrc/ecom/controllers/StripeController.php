<?php

namespace Mrc\Ecom\Controllers;

use Backend\Classes\Controller;
use Log;
use Illuminate\Http\Request;
use Queue;

class StripeController extends Controller
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     **/
    public function handleWebHook(Request $request)
    {
        Log::info(print_r($request->event->type, true));
        switch ($request->event->type) {
            case 'invoice.created':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoiceCreateWebhook', $request->event);
                break;
            case 'invoice.updated':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoiceUpdateWebhook', $request->event);
                break;
            case 'invoice.paid':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoicePaymentPaidWebhook', $request->event);
                break;
            case 'invoice.payment_succeeded':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoiceUpdateWebhook', $request->event);
                break;
            case 'invoice.payment_failed':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoicePaymentFailWebhook', $request->event);
                break;
            case 'invoice.finalized':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook\InvoiceUpdateWebhook', $request->event);
                break;
            case 'customer.subscription.updated':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\Webhook\SubscriptionUpdateWebhook', $request->event);
                break;
            case 'customer.subscription.deleted':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\Webhook\SubscriptionDeleteWebhook', $request->event);
            case 'customer.source.deleted':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Customer\Webhook\CustomerSourceDeleteWebhook', $request->event);
            case 'customer.source.created':
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Customer\Webhook\CustomerSourceCreateWebhook', $request->event);
            default:
                break;
        }
    }
}
