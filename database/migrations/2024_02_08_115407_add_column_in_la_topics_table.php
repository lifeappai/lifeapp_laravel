<?php

use App\Enums\GameType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInLaTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_topics', function (Blueprint $table) {
            $table->tinyInteger('type')->default(GameType::QUIZ)->comment("2=>quiz, 3=>riddle, 4=>puzzle");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_topics', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
