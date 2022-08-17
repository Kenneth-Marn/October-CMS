<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomUsersSubscriptions5 extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->boolean('cancelled')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_users_subscriptions', function($table)
        {
            $table->dropColumn('cancelled');
        });
    }
}
