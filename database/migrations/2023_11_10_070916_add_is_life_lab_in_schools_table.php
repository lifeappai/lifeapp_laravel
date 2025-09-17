<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLifeLabInSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->tinyInteger('is_life_lab')->default(1)->comment("1=>yes, 0=>no");
            $table->tinyInteger('app_visible')->default(1)->comment("1=>yes, 0=>no");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['is_life_lab', 'app_visible']);
        });
    }
}
