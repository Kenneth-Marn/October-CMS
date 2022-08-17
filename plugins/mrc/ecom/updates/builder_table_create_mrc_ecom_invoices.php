<?php namespace Mrc\Ecom\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMrcEcomInvoices extends Migration
{
    public function up()
    {
        Schema::create('mrc_ecom_invoices', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('billing_reason');
            $table->decimal('amount_due', 10, 0);
            $table->decimal('amount_paid', 10, 0);
            $table->decimal('amount_remaining', 10, 0);
            $table->integer('attempt_count');
            $table->boolean('paid');
            $table->decimal('total', 10, 0);
            $table->string('total_discount_amounts');
            $table->string('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('stripe_subscription_id');
            $table->string('stripe_invoice_id');
            $table->string('stripe_customer_id');
            $table->text('invoice_pdf')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mrc_ecom_invoices');
    }
}
