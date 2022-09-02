<?php

namespace Mrc\Ecom\Components;

use Cms\Classes\ComponentBase;
use Validator;
use ValidationException;
use Request;
use Exception;
use Queue;
use Auth;
use Mrc\Ecom\Models\Subscription as ModelSubscription;

class Subscription extends ComponentBase
{
    /**
     * Registers the hero to be accessible within the component
     * @var Mrc\Ecom\Models\Product
     */
    public $subscriptions;
    public $slug;

    public function componentDetails()
    {
        return [
            'name' => 'Subscription Component',
            'description' => 'Subscription Componenet'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $user = Auth::getUser();
        $this->subscriptions = ModelSubscription::where('user_id', $user->id)->whereNull('canceled_at')->get();
    }
    
    public function onAutoRechargeUpdate()
    {
        $data = Request::input();
        
        $rules = [
            'autoRecharge' => 'required',
            'subscriptionId'=> 'required'
        ];
        
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            try {
                $subscription = ModelSubscription::find($data['subscriptionId']);
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\UpdateSubscription', ['subscription' => $subscription, 'data' => $data]);
            } catch (Exception $e) {
                $message =  $e->getMessage();
                throw new ValidationException(['generalerror' => $message]);
            }
        }
    }
}
