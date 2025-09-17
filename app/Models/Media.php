<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string path
 */
class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path'
    ];

    /**
     * @return HasMany
     */
    public function missionQuestionTranslation(): HasMany
    {
        return $this->hasMany(missionQuestionTranslation::class, 'question_media_id');
    }

    public function getFullUrlAttribute()
    {
        return Storage::url($this->path);
    }

}
