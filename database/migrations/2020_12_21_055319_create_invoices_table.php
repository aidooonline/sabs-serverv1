<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('quote_id')->default(0);
            $table->integer('invoice_id');
            $table->string('name');
            $table->integer('salesorder')->default(0);
            $table->integer('quote')->default(0);
            $table->integer('opportunity')->default(0);
            $table->integer('status');
            $table->integer('account')->default(0);
            $table->float('amount');
            $table->date('date_quoted');
            $table->integer('quote_number');
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->integer('billing_postalcode')->default(0);
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_country')->nullable();
            $table->integer('shipping_postalcode')->default(0);
            $table->integer('billing_contact')->nullable();
            $table->integer('shipping_contact')->nullable();
            $table->string('tax');
            $table->string('shipping_provider');
            $table->string('description')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
