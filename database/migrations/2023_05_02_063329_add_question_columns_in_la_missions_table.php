<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionColumnsInLaMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->json('question')->nullable()->after('image');
            $table->json('document')->nullable()->after('image');
            $table->unsignedBigInteger('la_subject_id')->nullable()->after('image');
            $table->foreign('la_subject_id')->references('id')->on('la_subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->dropForeign('la_missions_la_subject_id_foreign');
            $table->dropColumn(['question', 'document', 'la_subject_id']);
        });
    }
}
