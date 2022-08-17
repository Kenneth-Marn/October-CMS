<?php

namespace Mrc\Ecom\Middleware;

use Stripe\Webhook;
use Log;
use Request;
use Closure;

class ValidateWebhookToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $endpoint_secret = getenv('ENDPOINT_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );

            if ($event) {
                $request->merge(['event' => $event]);
                return $next($request);
            } else {
                http_response_code(400);
                exit();
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::info($e->getMessage());
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::info($e->getMessage());
            http_response_code(400);
            exit();
        }

        Log::info($event->type);
    }
}
