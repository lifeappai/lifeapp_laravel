<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaMissionResource extends Model
{
    use HasFactory;

    public $fillable = [
        "la_mission_id",
        "title",
        "media_id",
        "locale",
        "index",
    ];

    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
    /**
     * @return BelongsTo
     */
    public function laMission(): BelongsTo
    {
        return $this->belongsTo(LaMission::class, 'la_mission_id');
    }

    public function getResourcesData($index)
    {
        return LaMissionResource::where('index', $index)->where('la_mission_id', $this->la_mission_id)->get();
    }
}
