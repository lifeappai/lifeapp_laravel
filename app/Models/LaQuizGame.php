<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;


/**
 * @property int $id
 * @property int $la_quiz_id
 * @property int $created_by
 * @property array $questions
 * @property int status
 */
class LaQuizGame extends Model
{
    protected $table = 'la_quiz_games';

    use HasFactory;

    protected $fillable = [
        'user_id',
        'la_subject_id',
        'la_topic_id',
        'type',
        'game_code',
        'time',
        'la_level_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'questions' => 'array'
    ];

    public function laSubject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laTopic(): BelongsTo
    {
        return $this->belongsTo(LaTopic::class, 'la_topic_id');
    }

    public function laLevel(): BelongsTo
    {
        return $this->belongsTo(LaLevel::class, 'la_level_id');
    }

    public function quizGameParticipants(): HasMany
    {
        return $this->hasMany(LaQuizGameParticipant::class, 'la_quiz_game_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'la_quiz_game_participants', 'la_quiz_game_id', 'user_id');
    }

    public function getUserQuizGameCoins(): HasOne
    {
        return $this->hasOne(LaQuizGameResult::class, 'la_quiz_game_id')->where('user_id', Auth::user()->id);
    }

    public function quiz()
    {
        return $this->belongsTo(LaQuiz::class, 'la_quiz_id');
    }

    public function laQuestions()
    {
        return $this->belongsToMany(LaQuestion::class, 'la_quiz_game_question_answers', 'la_quiz_game_id', 'la_question_id')
            ->withPivot('la_question_option_id', 'user_id', 'is_correct', 'coins');
    }
}
