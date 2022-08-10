<?php

namespace Mrc\Ecom\Components;

use Cms\Classes\ComponentBase;
use Mrc\Ecom\Models\Product;
use Validator;
use ValidationException;
use Request;
use Log;
use Exception;
use Stripe\StripeClient as StripeClient;
use Session;
use Queue;
use Auth;

class Transaction extends ComponentBase
{
    /**
     * Registers the hero to be accessible within the component
     * @var Mrc\Ecom\Models\Product
     */
    public $product;
    public $slug;

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
        $data = Request::input();
        $this->slug = $this->param('slug');

        $rules = [
            'stripeToken' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            try {
                $this->page['slug'] = $this->slug = $this->param('slug');
                $product = Product::where('slug', $this->slug)->first();
                
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\CreateSubscription', ['user' => $user, 'product' => $product, 'stripeToken' => $data['stripeToken']]);
            } catch (Exception $e) {
                $message =  $e->getMessage();
                throw new ValidationException(['generalerror' => $message]);
            }
        }



        //Log::info(print_r($customer, true));
    }
}
