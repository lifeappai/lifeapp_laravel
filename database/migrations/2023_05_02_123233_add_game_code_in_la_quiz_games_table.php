<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGameCodeInLaQuizGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_quiz_games', function (Blueprint $table) {
            $table->integer('game_code')->nullable()->after('time');
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
            $table->dropColumn('game_code');
        });
    }
}
