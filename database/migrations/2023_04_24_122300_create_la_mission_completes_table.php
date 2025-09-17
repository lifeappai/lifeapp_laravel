<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaMissionCompletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_mission_completes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_mission_id');
            $table->foreign('la_mission_id')->references('id')->on('la_missions')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('media_id');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->text('comments')->nullable();
            $table->integer('points')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
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
        Schema::dropIfExists('la_mission_completes');
    }
}
