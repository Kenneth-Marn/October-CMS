<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomProducts extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->string('slug');
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_products', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
