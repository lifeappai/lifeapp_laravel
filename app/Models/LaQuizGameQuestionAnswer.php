<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaQuizGameQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_quiz_game_id",
        "la_question_id",
        "user_id",
        "la_question_option_id",
        "is_correct",
        "coins",
    ];


    public function laQuizGame(): BelongsTo
    {
        return $this->belongsTo(LaQuizGame::class, 'la_quiz_game_id');
    }

    public function laQuizGameQuestion(): BelongsTo
    {
        return $this->belongsTo(LaQuestion::class, 'la_quiz_game_question_id');
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
            'answer_id' => $this->id,
            'game_code' => $this->game_code,
            'question_id' => $this->la_question_id,
            'answer_option_id' => $this->la_question_option_id,
            'id' => $this->laQuizGame->id,
            'time' => $this->laQuizGame ? $this->laQuizGame->time : '',
            'title' => $this->laQuestions ? $this->laQuestions->title : '',
        ];
    }

    public function laQuestions(): BelongsTo
    {
        return $this->belongsTo(LaQuestion::class, 'la_question_id');
    }
}
