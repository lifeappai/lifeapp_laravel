<?php

namespace App\Models;

use App\Http\Resources\MediaResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        "image",
        "allow_for",
        "title",
        "type",
        "la_subject_id",
        "la_level_id",
    ];

    protected $casts = [
        'title' => 'array',
        'image' => 'array',
    ];

    protected $appends = [
        'default_title'
    ];

    public function getDefaultTitleAttribute()
    {
        foreach ($this->title as $value) {
            return $value;
        }
        return '';
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image');
    }

    public static function getMediaPath($mediaId)
    {
        return Media::where('id', $mediaId)->first();
    }

    public static function getMediaResource($mediaId)
    {
        $media = Media::where('id', $mediaId)->first();
        return new MediaResource($media);
    }

    public function laTopicAssigns()
    {
        return $this->hasMany(LaTopicAssign::class, 'la_topic_id');
    }

    public function laQuizGames()
    {
        return $this->hasMany(LaQuizGame::class, 'la_topic_id');
    }
}
