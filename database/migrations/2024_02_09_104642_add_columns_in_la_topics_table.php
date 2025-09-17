<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInLaTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_topics', function (Blueprint $table) {
            $table->unsignedBigInteger('la_subject_id')->nullable();
            $table->foreign('la_subject_id')->references('id')->on('la_subjects');
            $table->unsignedBigInteger('la_level_id')->nullable();
            $table->foreign('la_level_id')->references('id')->on('la_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_topics', function (Blueprint $table) {
            $table->dropForeign(['la_topics_la_subject_id_foreign', 'la_topics_la_level_id_foreign']);
            $table->dropColumn(['la_subject_id', 'la_level_id']);
        });
    }
}
