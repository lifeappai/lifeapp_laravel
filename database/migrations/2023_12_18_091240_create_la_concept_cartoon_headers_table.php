<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaConceptCartoonHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_concept_cartoon_headers', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->string('button_one_text')->nullable();
            $table->text('button_one_link')->nullable();
            $table->string('button_two_text')->nullable();
            $table->text('button_two_link')->nullable();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->foreign('media_id')->references('id')->on('media');
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
        Schema::dropIfExists('la_concept_cartoon_headers');
    }
}
