<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMrcEcomInvoices extends Migration
{
    public function up()
    {
        Schema::table('mrc_ecom_invoices', function($table)
        {
            $table->dateTime('next_payment_attempt')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('mrc_ecom_invoices', function($table)
        {
            $table->dropColumn('next_payment_attempt');
        });
    }
}
