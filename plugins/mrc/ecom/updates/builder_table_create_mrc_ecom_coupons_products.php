<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomCouponsProducts extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_coupons_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('coupon_id');
            $table->integer('product_id');
            $table->primary(['coupon_id','product_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_coupons_products');
    }
}
