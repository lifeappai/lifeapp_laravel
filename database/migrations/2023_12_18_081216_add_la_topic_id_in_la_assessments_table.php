<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaTopicIdInLaAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_assessments', function (Blueprint $table) {
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_assessments', function (Blueprint $table) {
            $table->dropForeign('la_assessments_la_topic_id_foreign');
            $table->dropColumn(['la_topic_id']);
        });
    }
}
