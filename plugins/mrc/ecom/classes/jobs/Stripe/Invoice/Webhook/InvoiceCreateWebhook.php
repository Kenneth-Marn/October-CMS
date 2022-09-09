<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook;

use Log;
use Mrc\Ecom\Models\Invoice;
use Mrc\Ecom\Models\Subscription;


class InvoiceCreateWebhook
{
    public function fire($job, $event = null)
    {
        $invoiceObject = (object) $event['data']['object'];
        $stripeSubscriptionId = $invoiceObject->subscription;
    
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
        
        $invoice = Invoice::create([
            'user_id' => $subscription->user->id,
            'product_id' => $subscription->product->id,
            'billing_reason' => $invoiceObject->billing_reason,
            'amount_due' => $invoiceObject->amount_due/100,
            'amount_paid' => $invoiceObject->amount_paid/100,
            'amount_remaining' => $invoiceObject->amount_remaining/100,
            'attempt_count' => $invoiceObject->attempt_count,
            'paid' => $invoiceObject->paid/100,
            'total' => $invoiceObject->total/100,
            'subtotal' => $invoiceObject->subtotal/100,
            'total_discount_amounts' => $invoiceObject->total_discount_amounts,
            'status' => $invoiceObject->status,
            'stripe_subscription_id' => $invoiceObject->subscription,
            'stripe_customer_id' => $invoiceObject->customer,
            'stripe_invoice_id' => $invoiceObject->id,
            'invoice_pdf' => $invoiceObject->invoice_pdf,
            'start_at' => gmdate("Y-m-d H:i:s", $invoiceObject->period_start),
            'end_at' => gmdate("Y-m-d H:i:s", $invoiceObject->period_end),
            'next_payment_attempt' => gmdate("Y-m-d H:i:s", $invoiceObject->next_payment_attempt),
        ]);
        
        $job->delete();
    }
}
