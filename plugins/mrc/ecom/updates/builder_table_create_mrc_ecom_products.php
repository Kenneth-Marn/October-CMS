<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomProducts extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 0);
            $table->integer('recurring_month')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_products');
    }
}
