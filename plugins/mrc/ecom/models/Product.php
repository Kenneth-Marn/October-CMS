<?php

namespace Mrc\Ecom\Models;

use Model;
use Queue;

/**
 * Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'mrc_ecom_products';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $belongsToMany = [
        'users' => [
            'RainLab\User\Models\User',
            'table' => 'mrc_ecom_users_subscriptions',
            'key' => 'product_id',
            'otherKey' => 'user_id',
            'pivot' => ['start_date', 'end_date', 'disabled_at']
        ]
    ];

    public function afterCreate()
    {
        Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Product\CreateSubscriptionProduct', $this);
    }

    public function afterUpdate()
    {
        if ( $this->getOriginal('price') != $this->price || $this->getOriginal('recurring_month') != $this->recurring_month ) {
            Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Subscription\UpdateSubscriptionWithPriceChange', $this);
        } else {
            Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Product\UpdateSubscriptionProduct', $this);
        }
    }
}
