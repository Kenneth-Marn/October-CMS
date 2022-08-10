<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomUsersSubscriptions3 extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->string('stripe_subscription_id');
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->dropColumn('stripe_subscription_id');
        });
    }
}
