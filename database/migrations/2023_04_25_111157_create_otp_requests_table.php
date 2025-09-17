<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mobile_no');
            $table->integer('type')->comment("3=student, 4=mentor");
            $table->string('request_id')->nullable();
            $table->integer('status')->comment("1=success, -1=failed");
            $table->timestamp('veified_at')->nullable();
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
        Schema::dropIfExists('otp_requests');
    }
}
