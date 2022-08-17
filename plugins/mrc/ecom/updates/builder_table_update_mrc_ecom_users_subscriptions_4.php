<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomUsersSubscriptions4 extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->integer('user_id')->nullable()->change();
            $table->integer('product_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->integer('user_id')->nullable(false)->change();
            $table->integer('product_id')->nullable(false)->change();
        });
    }
}
