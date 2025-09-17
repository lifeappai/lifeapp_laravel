<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('la_section_id')->nullable();
            $table->foreign('la_section_id')->references('id')->on('la_sections')->onDelete('cascade');
            $table->string('guardian_name')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_la_section_id_foreign');
            $table->dropColumn(['la_section_id', 'guardian_name']);
        });
    }
}
