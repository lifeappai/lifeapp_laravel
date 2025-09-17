<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaLessionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_lession_plan_language_id",
        "la_board_id",
        "title",
        "document",
        "status",
        "type",
    ];

    public function laBoard(): BelongsTo
    {
        return $this->belongsTo(LaBoard::class, 'la_board_id');
    }

    public function laLessionPlanLanguage(): BelongsTo
    {
        return $this->belongsTo(LaLessionPlanLanguage::class, 'la_lession_plan_language_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'document');
    }
}
