<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToLaMissionCompletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_mission_completes', function (Blueprint $table) {
            //
            $table->Index(['approved_at']);
            $table->Index(['rejected_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_mission_completes', function (Blueprint $table) {
            //
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['rejected_at']);
        });
    }
}
