<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaMissionUserTiming extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "la_mission_resource_id",
        "timings",
    ];

    public function laMissionResource(): BelongsTo
    {
        return $this->belongsTo(LaMissionResource::class, 'la_mission_resource_id');
    }
}
