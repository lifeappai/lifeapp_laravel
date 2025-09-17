<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaQuizGameQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_quiz_game_id",
        "la_question_id",
    ];

    public function laQuestion(): BelongsTo
    {
        return $this->belongsTo(LaQuestion::class, 'la_question_id');
    }

    public function laQuizGame(): BelongsTo
    {
        return $this->belongsTo(LaQuizGame::class, 'la_quiz_game_id');
    }


    public function laQuizGameQuestionAnswers(): HasMany
    {
        return $this->hasMany(LaQuizGameQuestionAnswer::class, 'la_quiz_game_question_id');
    }
}
