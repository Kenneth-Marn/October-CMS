<?php


namespace Mrc\Ecom\Rules;

use Log;
use Auth;
use Mrc\Ecom\Models\Coupon;
use Mrc\Ecom\Models\Product;

class CouponValidateRule
{

    public $message = 'true';

    public function validate($attribute, $value, $params)
    {

        $user = Auth::getUser();
        [$slug] = $params;
        $product = Product::where('slug', $slug)->first();
        $coupon = Coupon::where(['code' => $value, 'active' => 1])->first();

        if ($coupon) {
            if ($this->verifyCouponWithCustomer($coupon, $user) && 
                $this->verifyCouponWithProduct($coupon, $product) && 
                $this->verifyCouponProperties($coupon)) {
                    
                   return true; 
            } else {
                return false;
            }
        } else {
            return false;
        }



        return false;
    }

    public function message()
    {
        return 'Invalid Coupon';
    }

    public function verifyCouponWithCustomer($coupon, $user)
    {
        if ($coupon->users()->exists()) {
            if ($coupon->users->contains($user->id)) {
                //current user is aviable for coupon
                return true;
            } else {
                //current user is not avaiable for coupon
                Log::error('current coupon is not compatible with the customer');
                return false;
            }
        } else {
            //can be used for all customers
            return true;
        }
    }

    public function verifyCouponWithProduct($coupon, $product)
    {
        if ($coupon->products()->exists()) {
            if ($coupon->products->contains($product->id)) {
                //current product is avilable for coupon
                return true;
            } else {
                //current product is not available for coupon
                Log::error('current coupon is not compatible with the product');
                return false;
            }
        } else {
            //can be used for all customers
            return true;
        }
    }

    public function verifyCouponProperties($coupon)
    {
        $usageCount = $coupon->subscriptions->count();
        
        if ($usageCount > $coupon->max_redemption) {
            Log::error('Usage exceeded');
            return false;
        }
       
        return true;
    }
}
