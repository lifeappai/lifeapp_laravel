<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaQueryRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_query_replies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('la_query_id');
            $table->unsignedBigInteger('user_id');
            $table->string('text');
            $table->unsignedBigInteger('media_id');

            $table->timestamps();

            $table->foreign('la_query_id')->references('id')->on('la_queries');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('la_query_replies');
    }
}
