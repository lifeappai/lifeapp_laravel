<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaLevelIdInLaQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_questions', function (Blueprint $table) {
            $table->dropColumn('level');
            $table->unsignedBigInteger('la_level_id')->nullable()->after('title');
            $table->foreign('la_level_id')->references('id')->on('la_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_questions', function (Blueprint $table) {
            $table->dropColumn('la_level_id');
            $table->integer('level')->default(1);
        });
    }
}
