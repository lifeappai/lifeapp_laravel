<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('school_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->string('mobile_no')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->date('dob')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->tinyInteger('grade')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->string('password')->nullable();
            $table->string('pin')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('otp')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
