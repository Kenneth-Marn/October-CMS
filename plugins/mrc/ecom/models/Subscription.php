<?php namespace Mrc\Ecom\Models;

use Model;
use Queue;

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
    public $rules = [
    ];
    
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
    
    public $hasMany = [
        'coupons' => [
            'Mrc\Ecom\Models\Coupon',
            'table' => 'mrc_ecom_subscriptions_coupons',
            'key' => 'subscription_id',
            'otherKey' => 'coupon_id',
            'pivot' => ['id', 'created_at', 'updated_at']
        ]
    ];
    
    public function afterCreate()
    {
        Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\CreateStripeSubscription', $this);
    }
}
