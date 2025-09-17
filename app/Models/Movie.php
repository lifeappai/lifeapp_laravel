<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Media media
 */
class Movie extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'topic_id',
        'title',
        'locale',
        'movie_media_id',
        'duration',
        'after_duration',
        'movie_type',
        'brain_points',
        'heart_points'
    ];
    /**
     * @return HasMany
     */
    public function topic() : HasMany
    {
        return $this->hasMany(Levels::class,'id','topic_id');
    }
    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'movie_media_id');
    }

    /**
     * @return HasMany
     */
    public function completed()
    {
        return $this->hasMany(MovieComplete::class, 'movie_id');
    }
}
