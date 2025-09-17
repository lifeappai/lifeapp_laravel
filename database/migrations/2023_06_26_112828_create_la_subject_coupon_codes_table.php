<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaSubjectCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('la_subject_coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('la_subject_id')->nullable();
            $table->foreign('la_subject_id')->references('id')->on('la_subjects')->onDelete('cascade');
            $table->string('coupon_code');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('unlock_coupon_at')->nullable();
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
        Schema::dropIfExists('la_subject_coupon_codes');
    }
}
