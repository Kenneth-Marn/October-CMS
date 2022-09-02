<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateUsersTableAddStripeCardId extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('stripe_card_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('stripe_card_id');
        });
    }
}
