<?php

namespace Mrc\Ecom\Classes\Jobs\Stripe\Invoice\Webhook;

use Log;
use Mrc\Ecom\Models\Invoice;

class InvoiceUpdateWebhook
{
    public function fire($job, $event = null)
    {
        $invoiceObject = (object) $event['data']['object'];
        
        Invoice::where('stripe_invoice_id', $invoiceObject->id)->update([
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
            'start_at' => gmdate("Y-m-d H:i:s", $invoiceObject->lines['data'][0]['period']['start']),
            'end_at' => gmdate("Y-m-d H:i:s", $invoiceObject->lines['data'][0]['period']['end']),
            'next_payment_attempt' => gmdate("Y-m-d H:i:s", $invoiceObject->next_payment_attempt),
        ]);
        
        $job->delete();
    }
}
