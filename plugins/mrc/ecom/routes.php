<?php

use Mrc\Ecom\Services\Stripe;
use Mrc\Ecom\Services\Mailer;
use Mrc\Ecom\Controllers\StripeController;
use Mrc\Ecom\Models\Invoice;

Route::post('/webhooks', [StripeController::class, 'handleWebHook'])->middleware('Mrc\Ecom\Middleware\ValidateWebhookToken');

Route::get('/test', function () {
    
    $invoice = Invoice::find(71);
    Mailer::sendInvoice($invoice);
    return 'Hello World';
});