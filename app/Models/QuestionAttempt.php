<?php

namespace App\Models;

use App\Http\Resources\MediaResource;
use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property QuizQuestion question
 */
class QuestionAttempt extends Model implements Coinable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
        'no_of_attempt',
        'rating',
        'point_type',
        'earn_point'
    ];

   /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "Question";
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return [
            'id' => $this->question->id,
            'locale' => $this->question->locale,
            'title' => $this->question->title,
            'type' => $this->question->type,
            'audio_media_id' => new MediaResource($this->question->audioMedia),
            'question_media_id' => new MediaResource($this->question->questionMedia)
        ];
    }
}
