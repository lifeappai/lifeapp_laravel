<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_subject_id')->nullable();
            $table->foreign('la_subject_id')->references('id')->on('la_subjects');
            $table->unsignedBigInteger('la_grade_id')->nullable();
            $table->foreign('la_grade_id')->references('id')->on('la_grades');
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
        Schema::dropIfExists('la_assessments');
    }
}
