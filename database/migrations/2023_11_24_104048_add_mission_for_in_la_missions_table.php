<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissionForInLaMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->tinyInteger('allow_for')->default(GameType::ALLOW_FOR['ALL'])->comment('1=>all, 2=by teacher');
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
            $table->dropColumn('allow_for');
        });
    }
}
