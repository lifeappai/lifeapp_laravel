<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Coupon;
use App\Models\User;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignIdFor(Coupon::class, 'coupon_id')->nullable()->constrained('coupons')->onDelete('restrict')->onUpdate('restrict');
            $table->string('title', 255)->nullable();
            $table->text('reason')->nullable();
            $table->unsignedInteger('coins')->default(0);
            $table->text('friend_request')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
}
