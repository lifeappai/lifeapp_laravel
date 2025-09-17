<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInLaLevelsTable extends Migration
{
      /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_levels', function (Blueprint $table) {
            $table->dropColumn('points');
            $table->json('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_levels', function (Blueprint $table) {
            $table->integer('points');
            $table->dropColumn('description');
        });
    }
}
