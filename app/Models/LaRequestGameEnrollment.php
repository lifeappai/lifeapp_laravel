<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaRequestGameEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "type",
        "la_game_enrollment_id",
        "approved_at",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laGameEnrollment(): BelongsTo
    {
        return $this->belongsTo(LaGameEnrollment::class, 'la_game_enrollment_id');
    }
}
