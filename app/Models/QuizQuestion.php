<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'quiz_id',
        'locale',
        'title',
        'type',
        'audio_media_id',
        'question_media_id',
        'answer',
    ];
     /**
     * @return HasMany
     */
    public function quiz() : HasMany
    {
        return $this->hasMany(Quiz::class,'id','quiz_id');
    }
   /**
     * @return HasMany
     */
    public function options() : HasMany
    {
        return $this->hasMany(QuestionOptions::class,'question_id');
    }
    /**
     * @return BelongsTo
     */
    public function questionMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'question_media_id');
    }
    /**
     * @return BelongsTo
     */
    public function audioMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'audio_media_id');
    }

  
}
