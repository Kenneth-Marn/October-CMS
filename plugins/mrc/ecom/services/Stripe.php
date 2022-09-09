<?php

namespace Mrc\Ecom\Services;

use Log;
use Mrc\Ecom\Models\Coupon;
use Mrc\Ecom\Models\Product;
use Stripe\StripeClient;
use RainLab\User\Models\User;
use Exception;
use Session;

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

    public function updateSubscription($data)
    {
        if ($data['data']['autoRecharge'] == 'false') {
            $subscription = $this->stripeClient->subscriptionSchedules->update(
                $data['subscription']['stripe_subscription_id'],
                [
                    'end_behavior' => 'cancel',

                ]
            );
        } else {
            $subscription = $this->stripeClient->subscriptionSchedules->update(
                $data['subscription']['stripe_subscription_id'],
                [
                    'end_behavior' => 'release',

                ]
            );
        }

        return $subscription;
    }

    public function createCustomer($data)
    {
        $user = User::find($data['id']);

        $forzenTime = strtotime("now");
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
        $customer = $this->stripeClient->customers->update(
            $user->stripe_customer_id,
            [
                'source' => $stripeToken
            ]
        );
        $user->stripe_card_id = $customer->default_source;
        $user->save();
        return $user;
    }

    public function createSubscription($model)
    {
        $user = $model->user;
        $product = $model->product;
        $subScription = $model;

        //if doesn't have stripe customer id
        if (is_null($user->stripe_customer_id)) {
            $user = $this->createCustomer($user);
        }

        //if payment is passed from the backend
        if ($model->payment) {
            $this->addSourceCustomer($user, $model->payment);    
        }
        
        try {
            $itemPayload['items'] = [
                [
                    'price' => $product->stripe_price_id,
                    'quantity' => 1,
                ],
            ];

            $payload = [
                'customer' => $user->stripe_customer_id,
                'start_date' => $subScription->start_date ? strtotime($subScription->start_date) : 'now'
            ];

            if ($subScription->end_date) {
                $payload['end_behavior'] = 'cancel';
                $itemPayload['end_date'] = strtotime($subScription->end_date);
            } else {
                $payload['end_behavior'] = 'release';
            }

            if (Session::has('couponCode')) {
                $couponId = Coupon::where('code', Session::get('couponCode'))->value('stripe_coupon_id');
                $itemPayload['coupon'] = $couponId;
                Session::forget('couponCode');
            }

            $payload['phases'] = [$itemPayload];

            Log::info(print_r($payload, true));
            
            $subscriptionSchedule = $this->stripeClient->subscriptionSchedules->create($payload);

        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            throw new \Exception($e->getMessage());
        } catch (Exception $e) {
            // Catch any other non-Stripe exceptions
            throw new \Exception($e->getMessage());
        }
        
        return $subscriptionSchedule;
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

    public function getCardDetails($user)
    {
        $carddetails = $this->stripeClient->customers->retrieveSource(
            $user['stripe_customer_id'],
            $user['stripe_card_id'],
        );

        return $carddetails;
    }

    public function updateCardDetails($user, $data)
    {
        $customer = $this->stripeClient->customers->update(
            $user->stripe_customer_id,
            ['source' => $data['stripeToken']]
        );

        $user->stripe_card_id = $customer->default_source;;
        $user->save();
        return $user;
    }
}
