<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;


class Quiz extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'movie_id',
        'locale',
        'brain_points',
        'heart_points',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class,'movie_id');
    }
    /**
     * @return HasMany
     */
    public function quizQuestions() : HasMany
    {
        return $this->hasMany(QuizQuestion::class,'quiz_id');
    }

    public function questionAttempts()
    {
        return $this->hasMany(QuestionAttempt::class, 'quiz_id');
    }

}
