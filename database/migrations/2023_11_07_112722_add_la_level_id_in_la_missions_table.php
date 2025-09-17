<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaLevelIdInLaMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->unsignedBigInteger('la_level_id')->nullable();
            $table->foreign('la_level_id')->references('id')->on('la_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->dropForeign('la_missions_la_level_id_foreign');
            $table->dropColumn(['la_level_id']);
        });
    }
}
