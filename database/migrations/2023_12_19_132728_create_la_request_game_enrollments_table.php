<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaRequestGameEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_request_game_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('type')->comment("1=>LIFE_LAB_DEMO_MODELS, 2=>JIGYASA_SELF_DIY_ACTVITES, 3=>PRAGYA_DIY_ACTIVITES_WITH_LIFE_LAB_KITS 4=>LIFE_LAB_ACTIVITIES_LESSION_PLANS, 5=>jigyasa, 6=>pragya");
            $table->unsignedBigInteger('la_game_enrollment_id')->nullable();
            $table->foreign('la_game_enrollment_id')->references('id')->on('la_game_enrollments')->onDelete('cascade');
            $table->timestamp("approved_at")->nullable();
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
        Schema::dropIfExists('la_request_game_enrollments');
    }
}
