<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('media_id')->nullable();
            $table->foreign('media_id')->references('id')->on('media');

            $table->unsignedBigInteger('school_id')->nullable();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->string('city');
            $table->string('state');

            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->json('users');
            $table->json('success_users');
            $table->json('failed_users');

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
        Schema::dropIfExists('push_notification_campaigns');
    }
}
