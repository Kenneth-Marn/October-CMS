<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomUsersSubscriptions extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->dateTime('disabled_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->dropColumn('disabled_at');
        });
    }
}
