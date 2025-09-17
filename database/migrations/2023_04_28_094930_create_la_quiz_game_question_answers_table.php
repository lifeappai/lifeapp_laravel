<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuizGameQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_quiz_game_question_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_quiz_game_id');
            $table->foreign('la_quiz_game_id')->references('id')->on('la_quiz_games')->onDelete('cascade');
            $table->unsignedBigInteger('la_question_id')->nullable();
            $table->foreign('la_question_id')->references('id')->on('la_questions')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('la_question_option_id');
            $table->foreign('la_question_option_id')->references('id')->on('la_question_options')->onDelete('cascade');
            $table->tinyInteger('is_correct')->default(1)->comment("1=>yes, 0=>no");
            $table->integer('coins')->default(0);
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
        Schema::dropIfExists('la_quiz_game_question_answers');
    }
}
