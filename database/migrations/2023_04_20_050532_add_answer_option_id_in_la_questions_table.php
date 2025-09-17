<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerOptionIdInLaQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('answer_option_id')->nullable();
            $table->foreign('answer_option_id')->references('id')->on('la_question_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_questions', function (Blueprint $table) {
            $table->dropForeign('la_questions_answer_option_id_foreign');
            $table->dropColumn('answer_option_id');
        });
    }
}
