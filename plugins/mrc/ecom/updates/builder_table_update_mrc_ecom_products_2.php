<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomProducts2 extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->string('stripe_price_id');
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->dropColumn('stripe_price_id');
        });
    }
}
