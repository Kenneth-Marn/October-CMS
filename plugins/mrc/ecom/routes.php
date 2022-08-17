<?php

use Mrc\Ecom\Services\Stripe;
use Mrc\Ecom\Controllers\StripeController;


Route::post('/webhooks', [StripeController::class, 'handleWebHook'])->middleware('Mrc\Ecom\Middleware\ValidateWebhookToken');