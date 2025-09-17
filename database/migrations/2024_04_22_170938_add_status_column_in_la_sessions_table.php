<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnInLaSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_sessions', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('date_time')->comment('0=>In-active, 1=>Active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_sessions', function (Blueprint $table) {
            $table->dropColumn("status");
        });
    }
}
