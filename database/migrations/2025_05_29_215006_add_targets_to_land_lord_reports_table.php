<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetsToLandLordReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('land_lord_reports', function (Blueprint $table) {
            $table->string('target_type')->nullable();
            $table->string('target_month')->nullable();
            $table->string('target_year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('land_lord_reports', function (Blueprint $table) {
            //
        });
    }
}
