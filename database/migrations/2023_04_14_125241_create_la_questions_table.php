<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('la_subject_id')->nullable();
            $table->foreign('la_subject_id')->references('id')->on('la_subjects')->onDelete('cascade');
            $table->tinyInteger('level')->default(1)->comment("1=>easy, 2=>moderate, 3=>hard, 4=>super hard");
            $table->json('title');
            $table->tinyInteger('status')->default(1)->comment("1=>active, 0=>deactive");
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
        Schema::dropIfExists('la_questions');
    }
}
