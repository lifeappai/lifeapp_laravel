<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionUpload extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'user_id',
        'question_media_id',
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
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
