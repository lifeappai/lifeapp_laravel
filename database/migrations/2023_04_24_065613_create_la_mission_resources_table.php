<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaMissionResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_mission_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_mission_id');
            $table->foreign('la_mission_id')->references('id')->on('la_missions')->onDelete('cascade');
            $table->string('title');
            $table->unsignedBigInteger('media_id');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->string('locale', 50)->nullable();
            $table->integer('index')->default(1);
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
        Schema::dropIfExists('la_mission_resources');
    }
}
