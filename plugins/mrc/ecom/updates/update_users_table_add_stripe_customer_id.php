<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateUsersTableAddStripeCustomerId extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('stripe_customer_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('stripe_customer_id');
        });
    }
}
