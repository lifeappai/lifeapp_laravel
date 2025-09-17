<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeInLaMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_missions', function (Blueprint $table) {
            $table->tinyInteger('type')->default(GameType::MISSION)->comment("1=>mission, 5=>jigyasa, 6=>pragya");
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
            $table->dropColumn('type');
        });
    }
}
