<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQueriesTableAddRating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_queries', function (Blueprint $table) {
            $table->string('feedback')->after('mentor_id')->nullable();
            $table->unsignedInteger('rating')->after('mentor_id')->nullable();
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
            $table->dropColumn('rating');
            $table->dropColumn('feedback');
        });
    }
}
