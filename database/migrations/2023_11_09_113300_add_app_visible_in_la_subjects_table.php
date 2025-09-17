<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppVisibleInLaSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->tinyInteger('app_visible')->default(1)->comment("1=>yes, 0=>no");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->dropColumn('app_visible');
        });
    }
}
