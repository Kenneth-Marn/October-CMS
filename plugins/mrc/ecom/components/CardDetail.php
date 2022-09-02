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
use Mrc\Ecom\Models\Subscription as ModelSubscription;
use Mrc\Ecom\Services\Stripe;

class CardDetail extends ComponentBase
{
    public $cardDetails;

    public function componentDetails()
    {
        return [
            'name' => 'CardDetail Component',
            'description' => 'CardDetail Componenet'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $user = Auth::getUser();
        $stripe = new Stripe;
        if ($user->stripe_card_id) {
            $this->cardDetails = $stripe->getCardDetails($user);
        }
        
        $this->page['stripePublicKey'] = $this->stripePublicKey = getenv('STRIPE_PUBLICKEY');
    }

    public function onUpdatePayment()
    {
        $user = Auth::getUser();
        $data = Request::input();
        $stripe = new Stripe;
        $rules = [
            'stripeToken' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            try {
                $this->cardDetails = $stripe->updateCardDetails($user, $data);
            } catch (Exception $e) {
                $message =  $e->getMessage();
                throw new ValidationException(['generalerror' => $message]);
            }
        }
    }
}
