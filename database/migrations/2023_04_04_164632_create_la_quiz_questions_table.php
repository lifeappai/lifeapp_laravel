<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('la_quiz_id');
            $table->string('default_text');
            $table->json('text');
            $table->unsignedSmallInteger('type')->default(0);
            $table->timestamps();
            $table->foreign('la_quiz_id')->on('la_quizzes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('la_quiz_questions');
    }
}
