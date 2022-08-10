<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomProducts3 extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->string('stripe_product_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->dropColumn('stripe_product_id');
        });
    }
}
