<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponColumnInLaSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('la_subjects', function (Blueprint $table) {
            $table->tinyInteger('is_coupon_available')->default(0)->comment("1 => yes, 0=> no");
            $table->integer('coupon_code_count')->default(0);
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
            $table->dropColumn(['is_coupon_available', 'coupon_code_count']);
        });
    }
}
