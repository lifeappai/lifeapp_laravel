<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->foreign('question_id')->references('id')->on('la_questions')->onDelete('cascade');
            $table->json('title');
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
        Schema::dropIfExists('la_question_options');
    }
}
