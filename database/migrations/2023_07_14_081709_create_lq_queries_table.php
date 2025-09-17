<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLqQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('mentor_id')->nullable();

            $table->unsignedBigInteger('la_subject_id');
            $table->string('description', 500);
            $table->unsignedBigInteger('media_id')->nullable();

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('mentor_id')->references('id')->on('users');
            $table->foreign('media_id')->references('id')->on('media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('la_queries');
    }
}
