<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeInLaQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_questions', function (Blueprint $table) {
            $table->tinyInteger('type')->default(GameType::QUIZ)->comment("2=>quiz, 3=>riddle, 4=puzzle");
            $table->tinyInteger('question_type')->default(GameType::QUESTION_TYPE['TEXT'])->comment("1=>text, 2=>image");
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
            $table->dropColumn(['type', 'question_type']);
        });
    }
}
