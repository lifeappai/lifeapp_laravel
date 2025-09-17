<?php

namespace App\Models;

use App\Http\Traits\LocaleTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionQuestionTranslation extends Model
{
    use HasFactory, Notifiable, LocaleTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'locale',
        'question_title',
        'question_media_id'
    ];
    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'question_media_id');
    }
   /**
     * @return BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }
}
