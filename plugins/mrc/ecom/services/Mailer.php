<?php

namespace Mrc\Ecom\Services;

use Log;
use Mail;
use Mrc\Ecom\Models\Subscription;

class Mailer extends Mail
{
    public static function sendInvoice($invoice)
    {
        $receiverEmail = $invoice->user->email;
        
        $data = [
            'invoice' => $invoice
        ];
        
        Mail::send(['text' => 'mrc.ecom::mail.NotifyUserInvoice'], $data, function ($message) use ($receiverEmail) {
            $message->to($receiverEmail, 'Myoresearch Cutomer');
            $message->subject('myoresearch.com notification: subscription has been paid');
            $message->from('noreply@myoresearch.com', 'myoresearch.com');
        });
    }
    
    public static function sendTestEmail() {
       $subscription = Subscription::find(88);
       $subscription->sendMail();
    }
    
    public static function notifyPaymentFail($invoice)
    {
        $receiverEmail = $invoice->user->email;
        $data = [
            'firstName' => $invoice->user->name,
            'productName' => $invoice->product->name,
            'startAt' => $invoice->start_at,
            'endAt' => $invoice->end_at,
            'unitPrice' => $invoice->product->price,
            'subtotal' => $invoice->subtotal,
            'total' => $invoice->total,
            'amountPaid' => $invoice->amount_paid,
            'amountRemaining' => $invoice->amount_remaining,
            'nextPaymentAttempt' => $invoice->next_payment_attempt,
            'paymentLink' => getenv('APP_URL') . getenv('CARD_DETAILS')
        ];
        
        Mail::send(['text' => 'mrc.ecom::mail.NotifyUserPaymentFail'], $data, function ($message) use ($receiverEmail) {
            $message->to($receiverEmail, 'Myoresearch Customer');
            $message->subject('myoresearch.com notification: payment failed');
            $message->from('noreply@myoresearch.com', 'myoresearch.com');
        });
    }
    
    public static function notifySubscriptionDelete($subscription)
    {
        $receiverEmail = $subscription->user->email;
        $data = [
            'firstName' => $subscription->user->name,
            'productName' => $subscription->product->name
        ];
        
        Mail::send(['text' => 'mrc.ecom::mail.NotifyUserPaymentFail'], $data, function ($message) use ($receiverEmail) {
            $message->to($receiverEmail, 'Myoresearch Customer');
            $message->subject('myoresearch.com notification: Subscription deleted');
            $message->from('noreply@myoresearch.com', 'myoresearch.com');
        });
    }
}