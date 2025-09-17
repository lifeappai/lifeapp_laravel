<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaQuizGameParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_quiz_game_id",
        "user_id",
        "status",
    ];

    protected $hidden = [
        'la_quiz_game_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laQuizGame(): BelongsTo
    {
        return $this->belongsTo(LaQuizGame::class, 'la_quiz_game_id');
    }

    public function laQuizGameQuestions(): BelongsTo
    {
        return $this->belongsTo(LaQuizGameQuestion::class, 'la_quiz_game_id');
    }
}
