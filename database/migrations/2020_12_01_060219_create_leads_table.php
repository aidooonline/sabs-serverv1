<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leads', function (Blueprint $table){
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->integer('account')->default(0);
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('title');
            $table->string('website');
            $table->text('lead_address')->nullable();
            $table->string('lead_city')->nullable();
            $table->string('lead_state')->nullable();
            $table->string('lead_country')->nullable();
            $table->integer('lead_postalcode')->default(0);
            $table->string('status', 20);
            $table->string('source');
            $table->float('opportunity_amount');
            $table->integer('campaign')->default(0);
            $table->string('industry')->nullable();
            $table->string('is_converted')->default(0);
            $table->string('description')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
