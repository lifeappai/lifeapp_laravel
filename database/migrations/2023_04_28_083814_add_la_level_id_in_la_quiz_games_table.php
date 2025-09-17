<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaLevelIdInLaQuizGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_quiz_games', function (Blueprint $table) {
            $table->unsignedBigInteger('la_level_id')->nullable()->after('time');
            $table->foreign('la_level_id')->references('id')->on('la_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_quiz_games', function (Blueprint $table) {
            $table->tinyInteger('level')->default(1);
            $table->dropColumn('la_level_id');
        });
    }
}
