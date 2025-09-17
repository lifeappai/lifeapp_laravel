<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaGameEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        "enrollment_code",
        "type",
        "user_id",
        "unlock_enrollment_at",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
