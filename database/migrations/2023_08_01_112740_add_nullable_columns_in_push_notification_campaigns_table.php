<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableColumnsInPushNotificationCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notification_campaigns', function (Blueprint $table) {
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_notification_campaigns', function (Blueprint $table) {
            $table->string('city')->change();
            $table->string('state')->change();
        });
    }
}
