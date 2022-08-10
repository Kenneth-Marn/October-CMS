<?php namespace Mrc\Ecom\Models;

use Model;

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
        'end_date'
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
        ]
    ];
}
