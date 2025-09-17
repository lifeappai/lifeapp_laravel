<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageColumnInLaSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->json("image")->after('title')->nullable();
            $table->json("heading")->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->dropColumn(['image', 'heading']);
        });
    }
}
