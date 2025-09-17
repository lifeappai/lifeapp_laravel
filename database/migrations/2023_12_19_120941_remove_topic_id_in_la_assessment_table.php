<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTopicIdInLaAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_assessments', function (Blueprint $table) {
            $table->dropForeign('la_assessments_la_topic_id_foreign');
            $table->dropColumn(['la_topic_id']);
        });
        Schema::table('la_work_sheets', function (Blueprint $table) {
            $table->dropForeign('la_work_sheets_la_topic_id_foreign');
            $table->dropColumn(['la_topic_id']);
        });
        Schema::table('la_concept_cartoons', function (Blueprint $table) {
            $table->dropForeign('la_concept_cartoons_la_topic_id_foreign');
            $table->dropColumn(['la_topic_id']);
        });
        Schema::table('la_competencies', function (Blueprint $table) {
            $table->dropForeign('la_competencies_la_topic_id_foreign');
            $table->dropColumn(['la_topic_id']);
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
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
        });
        Schema::table('la_work_sheets', function (Blueprint $table) {
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
        });
        Schema::table('la_concept_cartoons', function (Blueprint $table) {
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
        });
        Schema::table('la_competencies', function (Blueprint $table) {
            $table->unsignedBigInteger('la_topic_id')->nullable();
            $table->foreign('la_topic_id')->references('id')->on('la_topics')->onDelete('cascade');
        });
    }
}