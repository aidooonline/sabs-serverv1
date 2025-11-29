<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConvertedSalesorderIdInQuotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'quotes', function (Blueprint $table){
            $table->integer('converted_salesorder_id')->default(0)->after('description');
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
        Schema::table(
            'quotes', function (Blueprint $table){
            $table->dropColumn('converted_salesorder_id');
        }
        );
    }
}
