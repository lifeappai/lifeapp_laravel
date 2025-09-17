<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $user_id
 * @property $la_quiz_game_id
 */
class LaQuizGameInvite extends Model
{
    use HasFactory;

    protected $table = "la_quiz_game_invites";

    const STATUS_PENDING = 0;
    const STATUS_JOINED = 1;
    const STATUS_DENIED = -1;

    public function quizGame()
    {
        return $this->belongsTo(LaQuizGame::class, 'la_quiz_game_id');
    }
}
