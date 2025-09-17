<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaQuizGameResult extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_quiz_game_id",
        "user_id",
        "total_questions",
        "total_correct_answers",
        "coins",
    ];

    public function laQuizGame(): BelongsTo
    {
        return $this->belongsTo(LaQuizGame::class, 'la_quiz_game_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "Quiz";
    }


    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return [
            'result_id' => $this->id,
            'game_code' => $this->laQuizGame->game_code,
            'id' => $this->laQuizGame->id,
        ];
    }
}
