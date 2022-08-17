<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomUsersSubscriptions extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_users_subscriptions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('cancel_options', ['cancel_at_the_end', 'cancel_at_custom_date', 'cancel_immediately'])->nullable();
            $table->timestamp('cancel_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_users_subscriptions');
    }
}
