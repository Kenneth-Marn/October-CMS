<?php

namespace Mrc\Ecom\Services;

use Log;


class CouponService
{
    public static function calculateDiscountedPrice($product, $coupon)
    {
        $discountedPrice = 0;
        
        if ($coupon->type == '%') {
            $discountedPrice = self::getPercentageDiscountedPrice($product->price, $coupon->amount);
        } elseif ($coupon->type == '$') {
            $discountedPrice = self::getDollarDiscountPrice($product->price, $coupon->amount);
        } else {
            throw new \ErrorException('Invalid Coupon Type');
        }

        if ($coupon->duration == 'repeating') {
            $message = 'AUD' . $discountedPrice . ' for the first ' . $coupon->duration_in_months . ' billings.';
        } elseif ($coupon->duration == 'once') {
            $message = 'AUD' . $discountedPrice . ' for the first billing.';
        } elseif ($coupon->duration == 'forever') {
            $message = 'AUD' . $discountedPrice . ' for every billing.';
        } else {
            throw new \ErrorException('Invalid Coupon Duration');
        }

        return [
            'discountedPrice' => $discountedPrice,
            'message' => $message
        ];
    }

    private static function getPercentageDiscountedPrice($price, $amount)
    {
        $discountedPrice = $price - ($price * ($amount / 100));
        return $discountedPrice;
    }

    private static function getDollarDiscountPrice($price, $amount)
    {
        $discountedPrice = $price - $amount;
        Log::info($discountedPrice);
        return $discountedPrice;
    }
}
