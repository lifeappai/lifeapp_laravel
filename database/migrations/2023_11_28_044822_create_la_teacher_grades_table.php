<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaTeacherGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_teacher_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('la_subject_id');
            $table->foreign('la_subject_id')->references('id')->on('la_subjects')->onDelete('cascade');
            $table->unsignedBigInteger('la_grade_id');
            $table->foreign('la_grade_id')->references('id')->on('la_grades')->onDelete('cascade');
            $table->unsignedBigInteger('la_section_id');
            $table->foreign('la_section_id')->references('id')->on('la_sections')->onDelete('cascade');
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
        Schema::dropIfExists('la_teacher_grades');
    }
}
