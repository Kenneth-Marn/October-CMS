<?php namespace Mrc\Ecom\Models;

use Model;
use Queue;

/**
 * Model
 */
class Coupon extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'mrc_ecom_coupons';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    
    public $belongsToMany = [
        'users' => [
            'RainLab\User\Models\User',
            'table' => 'mrc_ecom_coupons_users',
            'key' => 'coupon_id',
            'otherKey' => 'user_id'
        ],
        'products' => [
            'Mrc\Ecom\Models\Product',
            'table' => 'mrc_ecom_coupons_products',
            'key' => 'coupon_id',
            'otherKey' => 'product_id'
        ]    
    ];
    
    public function afterCreate() {
        Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Coupon\CreateCoupon', $this);
    }
}
