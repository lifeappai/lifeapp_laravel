<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mission;
use App\Models\Media;


class CreateMissionUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mission::class, 'mission_id')->nullable()->constrained('missions')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignIdFor(Media::class, 'question_media_id')->nullable()->constrained('media')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('mission_uploads');
    }
}
