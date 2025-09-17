<?php

namespace App\Models;

use App\Http\Resources\MediaResource;
use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Movie movie
 */
class MovieUserTiming extends Model implements Coinable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'movie_id',
        'user_id',
        'duration',
        'no_of_view',
        'rating',
        'point_type',
        'earn_point',
    ];

   /**
     * @return BelongsTo
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "Movie";
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return [
            'id' => $this->movie->id,
            'title' => $this->movie->title,
            'media' => new MediaResource($this->movie->media)
        ];
    }
}
