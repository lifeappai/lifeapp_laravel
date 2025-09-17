<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMissionCompleteAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mission_completes', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('description');
            $table->timestamp('rejected_at')->nullable()->after('description');
            $table->string('comment')->nullable()->after('description')->comment('Teacher Comments');
            $table->unsignedSmallInteger('rating')->nullable()->after('description');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mission_completes', function (Blueprint $table) {
            $table->dropColumn('approved_at');
            $table->dropColumn('rejected_at');
            $table->dropColumn('comment');
            $table->dropColumn('rating');
        });
    }
}
