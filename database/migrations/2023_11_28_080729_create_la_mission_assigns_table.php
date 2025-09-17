<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaMissionAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_mission_assigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('la_mission_id');
            $table->foreign('la_mission_id')->references('id')->on('la_missions')->onDelete('cascade');
            $table->date('due_date');
            $table->tinyInteger('type')->default(GameType::MISSION)->comment("1=>mission, 5=>jigyasa, 6=>pragya");
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
        Schema::dropIfExists('la_mission_assigns');
    }
}
