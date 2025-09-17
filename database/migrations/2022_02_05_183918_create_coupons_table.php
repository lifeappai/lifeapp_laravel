<?php

use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->foreignIdFor(Category::class, 'category_id')->nullable()->constrained('categories')->onDelete('restrict')->onUpdate('restrict');
            $table->string('coin', 50)->nullable();
            $table->text('details')->nullable();
            $table->foreignIdFor(Media::class, 'coupon_media_id')->nullable()->constrained('media')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('coupons');
    }
}
