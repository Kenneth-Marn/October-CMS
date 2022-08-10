<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomCouponsUsers extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_coupons_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('coupon_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->primary(['coupon_id','user_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_coupons_users');
    }
}
