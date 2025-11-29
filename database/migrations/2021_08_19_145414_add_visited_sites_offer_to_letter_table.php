<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisitedSitesOfferToLetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            //
            $table->boolean('call_made')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('mail_sent')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('visited_site')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('offer_letter')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('contract')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('payment')->nullable()->default(0)->after('lead_temperature');
            $table->boolean('receipt')->nullable()->default(0)->after('lead_temperature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
}
