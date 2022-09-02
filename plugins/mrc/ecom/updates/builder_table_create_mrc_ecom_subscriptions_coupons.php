<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomSubscriptionsCoupons extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_subscriptions_coupons', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('subscription_id');
            $table->integer('coupon_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_subscriptions_coupons');
    }
}
