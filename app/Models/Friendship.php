<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer recipient_id
 * @property integer sender_id
 * @property string status
 * @property User sender
 */
class Friendship extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
