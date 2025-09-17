<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionUserTiming extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'mission_img_id',
        'user_id',
        'timing',
    ];

    protected $hidden = [
        'mission_id',
        'user_id',
        'created_at',
        'updated_at',
        'mission_img_id',
    ];

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
    public function missionImage(): BelongsTo
    {
        return $this->belongsTo(MissionImage::class, 'mission_img_id');
    }
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
