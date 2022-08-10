<?php

namespace Mrc\Ecom\Services;

use Auth;
use Log;
use Mrc\Ecom\Models\Coupon;
use Mrc\Ecom\Models\Product;
use Stripe\StripeClient as StripeClient;
use RainLab\User\Models\User;

class Stripe
{
    private $stripeClient;

    public function __construct()
    {
        $this->stripeClient =  new StripeClient(getenv('STRIPE_SECRETKEY'));
    }

    /**
     * 
     */
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

    public function createCustomer($data)
    {
        $user = User::find($data['id']);
        $stripeCustomer = $this->stripeClient->customers->create([
            'description' => $data['name'],
            'email' => $data['email'],
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

    public function createSubscription($user, $product)
    {
        $user = User::find($user['id']);
        $subscription = $this->stripeClient->subscriptions->create([
            'customer' => $user->stripe_customer_id,
            'items' => [
                ['price' => $product['stripe_price_id']],
            ],
        ]);

        return $subscription;
    }

    public function createCoupon($data)
    {
        $coupon = coupon::find($data['id']);
        //Log::info($data);
        if ($data['type'] == '$') {
            $payload['amount_off'] = $data['amount'];
        } else {
            $payload['percent_off'] = $data['amount'];
        }

        if ($data['type'] == 'repeating') {
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
}
