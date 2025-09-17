<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Subjects;

class CreateLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subjects::class, 'subject_id')->nullable()->constrained('subjects')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('level')->nullable();
            $table->tinyInteger('flag')->nullable();
            $table->string('description')->nullable();
            $table->string('total_rewards', 50)->nullable();
            $table->string('total_question', 50)->nullable();
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
        Schema::dropIfExists('levels');
    }
}
