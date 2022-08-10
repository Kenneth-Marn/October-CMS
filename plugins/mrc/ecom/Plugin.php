<?php namespace Mrc\Ecom;

use System\Classes\PluginBase;
use RainLab\User\Models\User;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            '\Mrc\Ecom\Components\ProductList' => 'productlist',
            '\Mrc\Ecom\Components\Transaction' => 'transaction',
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
        });
    }
}
