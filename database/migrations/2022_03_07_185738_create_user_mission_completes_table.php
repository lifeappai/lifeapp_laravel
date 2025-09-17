<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMissionCompletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_mission_completes', function (Blueprint $table) {
            $table->id();
            $table->integer('mission_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('rating')->nullable();
            $table->string('mission_type', 100)->nullable();
            $table->integer('earn_points')->nullable();
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
        Schema::dropIfExists('user_mission_completes');
    }
}
