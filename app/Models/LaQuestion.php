<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'la_subject_id',
        'la_level_id',
        'la_topic_id',
        'type',
        'question_type',
        'title',
        'status',
        'answer_option_id',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laTopic(): BelongsTo
    {
        return $this->belongsTo(LaTopic::class, 'la_topic_id');
    }

    public function questionOptions(): HasMany
    {
        return $this->hasMany(LaQuestionOption::class, 'question_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(LaQuestionOption::class, 'question_id');
    }

    public function questionAnswer(): BelongsTo
    {
        return $this->belongsTo(LaQuestionOption::class, 'answer_option_id');
    }

    public function laLevel(): BelongsTo
    {
        return $this->belongsTo(LaLevel::class, 'la_level_id');
    }

    public function quizGameQuestionAnswer()
    {
        return $this->hasMany(LaQuizGameQuestionAnswer::class, 'la_question_id');
    }

    public function getMedia($mediaId)
    {
        return Media::find($mediaId);
    }
}
