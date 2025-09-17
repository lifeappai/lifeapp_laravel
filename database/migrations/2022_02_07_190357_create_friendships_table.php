<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class CreateFriendshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'sender_id')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignIdFor(User::class, 'recipient_id')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('restrict');
            $table->enum('status', ['pending', 'confirmed', 'blocked']);
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
        Schema::dropIfExists('friendships');
    }
}
