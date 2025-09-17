<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaLessionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_lession_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_board_id')->nullable();
            $table->foreign('la_board_id')->references('id')->on('la_boards');
            $table->unsignedBigInteger('la_lession_plan_language_id')->nullable();
            $table->foreign('la_lession_plan_language_id')->references('id')->on('la_lession_plan_languages');
            $table->string('title');
            $table->string('document');
            $table->tinyInteger('type');
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
        Schema::dropIfExists('la_lession_plans');
    }
}
