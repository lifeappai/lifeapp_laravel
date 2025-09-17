<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLaQueriesTableAddWaiting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_queries', function (Blueprint $table) {
            $table->tinyInteger('waiting_reply')->default(1)->after('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_queries', function (Blueprint $table) {
            $table->dropColumn('waiting_reply');
        });
    }
}
