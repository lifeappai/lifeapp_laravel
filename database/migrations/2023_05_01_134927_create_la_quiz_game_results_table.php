<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuizGameResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_quiz_game_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_quiz_game_id');
            $table->foreign('la_quiz_game_id')->references('id')->on('la_quiz_games')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('total_questions')->default(0)->nullable();
            $table->integer('total_correct_answers')->default(0)->nullable();
            $table->integer('coins')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('la_quiz_game_results');
    }
}
