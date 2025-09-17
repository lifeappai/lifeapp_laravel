<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_competencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_subject_id')->nullable();
            $table->foreign('la_subject_id')->references('id')->on('la_subjects');
            $table->unsignedBigInteger('la_level_id')->nullable();
            $table->foreign('la_level_id')->references('id')->on('la_levels');
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics');
            $table->string('title');
            $table->string('document');
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
        Schema::dropIfExists('la_competencies');
    }
}
