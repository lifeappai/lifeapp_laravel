<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mission;
use App\Models\Media;

class CreateMissionQuestionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_question_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mission::class, 'mission_id')->nullable()->constrained('missions')->onDelete('restrict')->onUpdate('restrict');
            $table->string('locale', 50)->nullable();
            $table->string('question_title', 100)->nullable();
            $table->foreignIdFor(Media::class, 'question_media_id')->nullable()->constrained('media')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_question_translations');
    }
}
