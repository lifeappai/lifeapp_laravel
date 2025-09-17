<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaTopicAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_topic_assigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('la_topic_id');
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
            $table->date('due_date');
            $table->tinyInteger('type')->default(GameType::QUIZ)->comment("2=>quiz, 3=>riddle, 4=puzzle");
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
        Schema::dropIfExists('la_topic_assigns');
    }
}
