<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('campaign')->default(0);
            $table->string('name');
            $table->integer('account')->default(0);
            $table->integer('stage');
            $table->float('amount');
            $table->string('probability');
            $table->string('close_date');
            $table->integer('contact')->default(0);
            $table->string('lead_source');
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
        Schema::dropIfExists('opportunities');
    }
}
