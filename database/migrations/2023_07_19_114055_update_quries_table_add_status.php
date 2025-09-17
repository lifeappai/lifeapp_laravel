<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuriesTableAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_queries', function (Blueprint $table) {
            $table->unsignedSmallInteger('status_id')->after('la_subject_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_queries', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
}
