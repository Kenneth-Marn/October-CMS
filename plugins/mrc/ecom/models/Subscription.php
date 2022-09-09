<?php

namespace Mrc\Ecom\Models;

use Model;
use Mrc\Ecom\Services\Stripe;
use Validator;
use ValidationException;
use Session;

/**
 * Model
 */
class Subscription extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mrc_ecom_users_subscriptions';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $fillable = [
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'stripe_subscription_id',
        'cancel_options',
        'cancel_at',
        'canceled_at',
        'next_recharge_date',
        'stripe_subscription_schedule_id',
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $belongsTo = [
        'user' => [
            'RainLab\User\Models\User',
            'table' => 'users',
            'key' => 'user_id'
        ],
        'product' => [
            'Mrc\Ecom\Models\Product',
            'table' => 'mrc_ecom_products',
            'key' => 'product_id'
        ],
    ];

    public $belongsToMany = [
        'coupons' => [
            'Mrc\Ecom\Models\Coupon',
            'table' => 'mrc_ecom_subscriptions_coupons',
            'key' => 'subscription_id',
            'otherKey' => 'coupon_id',
            'pivot' => ['id', 'created_at', 'updated_at']
        ]
    ];

    public function beforeCreate()
    {
        //Validate Coupons
        if ($this->coupons) {

            foreach ($this->coupons as $coupon) {
                $rules = [
                    'couponCode' => 'nullable|couponvalidate:' . $this->product->slug . ',' . $coupon->code
                ];
                
                $data['couponCode'] = $coupon->code;
                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                } else {
                    Session::put('couponCode', $coupon->code);
                }
            }
        }
        
        //Create Subscription on stripe
        $stripe = new Stripe;
        $subscriptionSchedule = $stripe->createSubscription($this);
        
        //Create subscription on site
        if ($subscriptionSchedule) {
            unset($this->payment);
            $this->start_date = gmdate("Y-m-d H:i:s", $subscriptionSchedule->current_phase->start_date);
            $this->end_date = gmdate("Y-m-d H:i:s", $subscriptionSchedule->current_phase->end_date);
            $this->next_recharge_date =  gmdate("Y-m-d H:i:s", $subscriptionSchedule->current_phase->end_date);
            $this->stripe_subscription_id = $subscriptionSchedule->subscription;
            $this->stripe_subscription_schedule_id = $subscriptionSchedule->id;
        } else {
            throw new \Exception("Invalid Model!");
        }
    }
}
