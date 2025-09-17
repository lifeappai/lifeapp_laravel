<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mission;
use App\Models\Media;

class CreateMissionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mission::class, 'mission_id')->nullable()->constrained('missions')->onDelete('restrict')->onUpdate('restrict');
            $table->string('locale', 50)->nullable();
            $table->foreignIdFor(Media::class, 'mission_media_id')->nullable()->constrained('media')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_images');
    }
}
