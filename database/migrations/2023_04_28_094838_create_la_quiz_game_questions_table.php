<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuizGameQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_quiz_game_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_quiz_game_id');
            $table->foreign('la_quiz_game_id')->references('id')->on('la_quiz_games')->onDelete('cascade');
            $table->unsignedBigInteger('la_question_id')->nullable();
            $table->foreign('la_question_id')->references('id')->on('la_questions')->onDelete('cascade');
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
        Schema::dropIfExists('la_quiz_game_questions');
    }
}
