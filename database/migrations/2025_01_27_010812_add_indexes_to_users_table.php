<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->index('mobile_no'); 
            $table->index('state');     
            $table->index('city');      
            $table->index('type');      
            $table->index('grade');
            $table->index('school_code');   
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
            //
            $table->dropIndex(['mobile_no']);
            $table->dropIndex(['state']);
            $table->dropIndex(['city']);
            $table->dropIndex(['type']);
            $table->dropIndex(['grade']);
            $table->dropIndex(['school_code']);
        });
    }
}
