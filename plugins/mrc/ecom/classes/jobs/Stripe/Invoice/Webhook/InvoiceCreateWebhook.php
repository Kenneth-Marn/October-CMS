<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook;

use Log;
use Mrc\Ecom\Models\Invoice;
use Mrc\Ecom\Models\Subscription;
use Mrc\Ecom\Services\Stripe;
use RainLab\User\Models\User;

class InvoiceCreateWebhook
{
    public function fire($job, $event = null)
    {
        $invoiceObject = (object) $event['data']['object'];
        $stripeSubscriptionId = $invoiceObject->subscription;
        
        Log::info($stripeSubscriptionId);
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
        
        Invoice::create([
            'user_id' => $subscription->user->id,
            'product_id' => $subscription->product->id,
            'billing_reason' => $invoiceObject->billing_reason,
            'amount_due' => floatval($invoiceObject->amount_due/100),
            'amount_paid' => floatval($invoiceObject->amount_paid/100),
            'amount_remaining' => floatval($invoiceObject->amount_remaining/100),
            'attempt_count' => $invoiceObject->attempt_count,
            'paid' => $invoiceObject->paid,
            'total' => $invoiceObject->total,
            'total_discount_amounts' => $invoiceObject->total_discount_amounts,
            'status' => $invoiceObject->status,
            'stripe_subscription_id' => $invoiceObject->subscription,
            'stripe_customer_id' => $invoiceObject->customer,
            'stripe_invoice_id' => $invoiceObject->id,
            'invoice_pdf' => $invoiceObject->invoice_pdf
        ]);
        $job->delete();
    }
}
