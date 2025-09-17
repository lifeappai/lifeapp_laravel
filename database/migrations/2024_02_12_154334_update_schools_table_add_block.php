<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchoolsTableAddBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string("district")->after("city")->nullable();
            $table->string("block")->after("city")->nullable();
            $table->string("cluster")->after("city")->nullable();
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
            $table->dropColumn("district");
            $table->dropColumn("block");
            $table->dropColumn("cluster");
        });
    }
}
