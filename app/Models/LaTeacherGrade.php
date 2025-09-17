<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaTeacherGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "la_grade_id",
        "la_section_id",
        "la_subject_id",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laSection(): BelongsTo
    {
        return $this->belongsTo(LaSection::class, 'la_section_id');
    }

    public function laGrade(): BelongsTo
    {
        return $this->belongsTo(LaGrade::class, 'la_grade_id');
    }

    public function laSubject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }
}
