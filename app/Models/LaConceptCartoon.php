<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaConceptCartoon extends Model
{
    use HasFactory;


    public $fillable = [
        "title",
        "document",
        "la_subject_id",
        "la_level_id",
        "status",
    ];



    public function subject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laLevel(): BelongsTo
    {
        return $this->belongsTo(LaLevel::class, 'la_level_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'document');
    }
}
