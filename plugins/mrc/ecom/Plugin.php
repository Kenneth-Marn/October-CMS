<?php namespace Mrc\Ecom;

use Mrc\Ecom\Rules\CouponValidateRule;
use System\Classes\PluginBase;
use RainLab\User\Models\User;
use Log;
use Validator;
use Queue;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            '\Mrc\Ecom\Components\ProductList' => 'productlist',
            '\Mrc\Ecom\Components\Transaction' => 'transaction',
            '\Mrc\Ecom\Components\Subscription' => 'subscription',
            '\Mrc\Ecom\Components\CardDetail' => 'carddetail',
            '\Mrc\Ecom\Components\InvoiceList' => 'invoicelist',
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Mrc\Ecom\FormWidgets\Userbox' => [
                'label' => 'Userbox field',
                'code' => 'userbox'
            ],
            'Mrc\Ecom\FormWidgets\Paymentbox' => [
                'label' => 'Paymentbox field',
                'code' => 'paymentbox'
            ]
        ];
    }
    public function registerSettings()
    {
    }
    
    public function boot()
    {
        User::extend(function ($model) {
            // Add the relations
            $model->hasOne['product'] = [
                'Mrc\Ecom\Models\Product',
                'table' => 'mrc_ecom_users_subscriptions',
                'key' => 'user_id',
                'otherKey' => 'product_id',
                'pivot' => ['start_date', 'end_date', 'disabled_at']
            ];
            
            $model->bindEvent('model.afterCreate', function () use ($model){
                Queue::push('Mrc\Ecom\Classes\Jobs\Stripe\Customer\CreateCustomer', $model);
            });
            
            $model->bindEvent('model.getFullNameAttribute', function () use ($model){
                return $model->name . " ";
            });
        });
       
        Validator::extend('couponvalidate', CouponValidateRule::class);
    }
}
