<?php namespace Mrc\Ecom\Models;

use Model;

/**
 * Model
 */
class Invoice extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    protected $jsonable = ['total_discount_amounts'];
    
    public $fillable = [
        'user_id',
        'product_id',
        'billing_reason',
        'amount_due',
        'amount_paid',
        'amount_remaining',
        'attempt_count',
        'paid',
        'total',
        'total_discount_amounts',
        'status',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_invoice_id',
        'invoice_pdf'
    ];
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'mrc_ecom_invoices';

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
