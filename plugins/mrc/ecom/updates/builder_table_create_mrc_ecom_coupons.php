<?php

namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomCoupons extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_coupons', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->string('code');
            $table->enum('type', ['$', '%']);
            $table->enum('duration', ['once', 'repeating', 'forever']);
            $table->integer('duration_in_months')->nullable();
            $table->decimal('amount', 10, 0);
            $table->integer('max_redemption');
            $table->boolean('active');
            $table->string('stripe_coupon_id');
            $table->string('stripe_promotion_code_id');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_till')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mrc_ecom_coupons');
    }
}
