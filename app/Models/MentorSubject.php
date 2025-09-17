<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "la_subject_id",
    ];

    public function laSubject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }
}
