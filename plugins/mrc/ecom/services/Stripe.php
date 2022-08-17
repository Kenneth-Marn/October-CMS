<?php

namespace Mrc\Ecom\Services;

use Auth;
use Log;
use Mrc\Ecom\Models\Coupon;
use Mrc\Ecom\Models\Product;
use Stripe\StripeClient;
use RainLab\User\Models\User;
use Exception;

class Stripe
{
    private $stripeClient;

    public function __construct()
    {
        $this->stripeClient =  new StripeClient(getenv('STRIPE_SECRETKEY'));
    }

    public function createSubscriptionProduct($model)
    {
        $product = Product::find($model['id']);
        $stripeProduct = $this->stripeClient->products->create([
            'name' => $model['name'],
            'description' => $model['description']
        ]);

        $stripePrice = $this->stripeClient->prices->create([
            'currency' => 'aud',
            'unit_amount' => $model['price'] * 100,
            'recurring' => [
                'interval' => 'month',
                'interval_count' => $model['recurring_month']
            ],
            'product' => $stripeProduct->id
        ]);

        $product->stripe_product_id = $stripeProduct->id;
        $product->stripe_price_id = $stripePrice->id;
        $product->save();
    }

    public function updateSubscriptionProduct($model)
    {
        $stripeProduct = $this->stripeClient->products->update(
            $model['stripe_product_id'],
            [
                'name' => $model['name'],
                'description' => $model['description']
            ]
        );
    }

    public function updateSubscriptionWithPriceChange($model)
    {
        $product = Product::find($model['id']);
        $stripeProduct = $this->stripeClient->products->update(
            $model['stripe_product_id'],
            [
                'name' => $model['name'],
                'description' => $model['description']
            ]
        );

        $stripePrice = $this->stripeClient->prices->create([
            'currency' => 'aud',
            'unit_amount' => $model['price'] * 100,
            'recurring' => [
                'interval' => 'month',
                'interval_count' => $model['recurring_month']
            ],
            'product' => $stripeProduct->id
        ]);

        $product->stripe_price_id = $stripePrice->id;
        $product->save();
    }

    public function updateSubscription($model)
    {
        $today = time();

        if ($model['cancel_options']) {
            if ($model['cancel_options'] == 'cancel_at_the_end') {
                $subscription = $this->stripeClient->subscriptions->update(
                    $model['stripe_subscription_id'],
                    [
                        'cancel_at_period_end' => true,
                        //'cancel_at' => $model['cancelled'] ? 1695168386 : null
                    ]
                );
            } elseif ($model['cancel_options'] == 'cancel_at_custom_date') {
                $subscription = $this->stripeClient->subscriptions->update(
                    $model['stripe_subscription_id'],
                    [
                        'cancel_at_period_end' => false,
                        'cancel_at' => strtotime($model['cancel_at'])
                    ]
                );
            } elseif ($model['cancel_options'] == 'cancel_immediately') {
                $this->stripeClient->subscriptions->cancel(
                    $model['stripe_subscription_id']
                );
            }
        }

        return $subscription;
    }

    public function createCustomer($data)
    {
        $user = User::find($data['id']);

        $forzenTime = strtotime('2022-08-16 00:00:01');
        $testClock = $this->stripeClient->testHelpers->testClocks->create([
            'frozen_time' => $forzenTime,
        ]);

        $stripeCustomer = $this->stripeClient->customers->create([
            'description' => $data['name'],
            'email' => $data['email'],
            'test_clock' => $testClock->id
        ]);

        $user->stripe_customer_id = $stripeCustomer->id;
        $user->save();
        return $user;
    }

    public function addSourceCustomer($data, $stripeToken)
    {
        $user = User::find($data['id']);
        $this->stripeClient->customers->update(
            $user->stripe_customer_id,
            [
                'source' => $stripeToken
            ]
        );
    }

    public function createSubscription($user, $product, $code = null)
    {
        $user = User::find($user['id']);

        try {

            $payload = [
                'customer' => $user->stripe_customer_id,
                'items' => [
                    ['price' => $product['stripe_price_id']],
                ],
            ];

            if ($code) {
                $coupon = Coupon::where('code', $code)->first();
                $payload['promotion_code'] = $coupon->stripe_promotion_code_id;
            }

            $subscription = $this->stripeClient->subscriptions->create($payload);
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            echo ($e->getMessage());
        } catch (Exception $e) {
            // Catch any other non-Stripe exceptions
            echo ($e->getMessage());
        }

        return $subscription;
    }

    public function createCoupon($data)
    {
        $coupon = coupon::find($data['id']);

        if ($data['type'] == '$') {
            $payload['amount_off'] = $data['amount'] * 100;
        } else {
            $payload['percent_off'] = $data['amount'];
        }

        if ($data['duration'] == 'repeating') {
            $payload['duration_in_months'] = $data['duration_in_months'];
        }

        $stripeCoupon = $this->stripeClient->coupons->create(array_merge($payload, [
            'name' => $data['name'],
            'duration' => $data['duration'],
            'currency' => 'AUD',
            'max_redemptions' => $data['max_redemption']
        ]));

        $stripePromoCode = $this->stripeClient->promotionCodes->create([
            'coupon' => $stripeCoupon->id,
            'code' => $data['code']
        ]);

        $coupon->stripe_coupon_id = $stripeCoupon->id;
        $coupon->stripe_promotion_code_id = $stripePromoCode->id;
        $coupon->save();
    }

    public function moveTestClock($data)
    {
        Log::info($data);
        $newTestClock = $this->stripeClient->testHelpers->testClocks->advance(
            $data['testClock'],
            ['frozen_time' => $data['newTime']]
        );

        Log::info(print_r($newTestClock, true));
        return 1;
    }
}
