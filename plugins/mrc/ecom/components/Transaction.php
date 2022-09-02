<?php

namespace Mrc\Ecom\Components;

use Cms\Classes\ComponentBase;
use Mrc\Ecom\Models\Product;
use Validator;
use ValidationException;
use Request;
use Log;
use Exception;
use Mrc\Ecom\Models\Subscription;
use Queue;
use Auth;
use Mrc\Ecom\Models\Coupon;
use Mrc\Ecom\Services\CouponService;
use Redirect;
use Session;

class Transaction extends ComponentBase
{
    /**
     * Registers the hero to be accessible within the component
     * @var Mrc\Ecom\Models\Product
     */
    public $product;
    public $slug;
    public $price;

    public function componentDetails()
    {
        return [
            'name' => 'Transaction Component',
            'description' => 'Transaction Componenet'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->page['slug'] = $this->slug = $this->param('slug');
        $product = Product::where('slug', $this->slug)->first();
        $this->page['stripePublicKey'] = $this->stripePublicKey = getenv('STRIPE_PUBLICKEY');

        if ($product) {
            $this->page['product'] = $this->product = $product;
            $this->page['price'] = $this->price = $product->price;
        } else {
            return $this->controller->run('404');
        }
    }


    /**
     * Executed when Puchase happen
     * Goal : charge the user. Create Transaction record. Send email to user with access code.
     * @return  void
     */
    public function onPurchaseSubmit()
    {
        $user = Auth::getUser();
        $product = Product::where('slug', $this->param('slug'))->first();
        $data = Request::input();

        $rules = [
            'stripeToken' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            try {

                try {
                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'proration_behavior' => 'create_prorations'
                    ]);

                    Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\CreateSubscription', ['user' => $user, 'product' => $product, 'data' => $data]);
                    return Redirect::to('/subscriptions');
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    throw new ValidationException(['generalerror' => $e->getMessage()]);
                }
            } catch (Exception $e) {
                $message =  $e->getMessage();
                throw new ValidationException(['generalerror' => $message]);
            }
        }
    }

    public function onApplyCoupon()
    {
        $data = Request::input();

        $rules = [
            'couponCode' => 'nullable|couponvalidate:' . $this->param('slug')
        ];

        $product = Product::where('slug', $this->param('slug'))->first();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            $coupon = Coupon::where(['code' => $data['couponCode'], 'active' => 1])->first();

            try {
                $couponData = CouponService::calculateDiscountedPrice($product, $coupon);
                Session::put('couponCode', $data['couponCode']);
                $this->page['price'] = $this->price = $couponData['discountedPrice'];
                $this->page['couponMessage'] = $this->couponMessage = $couponData['message'];
            } catch (\ErrorException $e) {
                Log::error($e->getMessage());
                $this->page['couponMessage'] = $this->couponMessage = $e->getMessage();
            }
        }
    }

    public function onRemoveCoupon()
    {
        Session::forget('couponCode');
        $product = Product::where('slug', $this->param('slug'))->first();
        $this->page['price'] = $this->price = $product->price;
        $this->page['couponMessage'] = $this->couponMessage = null;
    }
}
