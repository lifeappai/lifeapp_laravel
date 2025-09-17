<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuizOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_quiz_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_quiz_question_id');
            $table->string('default_text');
            $table->json('text');
            $table->timestamps();
            $table->foreign('la_quiz_question_id')->on('la_quiz_questions')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('la_quiz_options');
    }
}
