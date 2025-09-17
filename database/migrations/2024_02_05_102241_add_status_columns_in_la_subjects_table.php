<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnsInLaSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->tinyInteger('mission_status')->default(1)->nullable();
            $table->tinyInteger('quiz_status')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->dropColumn(['mission_status', 'quiz_status']);
        });
    }
}
