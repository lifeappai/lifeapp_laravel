<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsTimeInLaLevelsTable extends Migration
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
            $table->integer('mission_points')->default(0);
            $table->integer('quiz_points')->default(0);
            $table->integer('riddle_points')->default(0);
            $table->integer('puzzle_points')->default(0);
            $table->integer('jigyasa_points')->default(0);
            $table->integer('pragya_points')->default(0);
            $table->integer('quiz_time')->default(60);
            $table->integer('riddle_time')->default(60);
            $table->integer('puzzle_time')->default(60);
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
            $table->dropColumn(['mission_points', 'quiz_points', 'riddle_points', 'puzzle_points', 'jigyasa_points', 'pragya_points', 'quiz_time', 'riddle_time', 'puzzle_time']);
            $table->integer('points')->default(0);
        });
    }
}
