<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\API\V1\MediaResource;

class LaSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'heading',
        'image',
        'status',
        'is_coupon_available',
        'coupon_code_count',
        'mission_status',
        'quiz_status',
        'meta_title',
    ];

    protected $casts = [
        'title' => 'array',
        'image' => 'array',
        'heading' => 'array',
    ];

    protected $appends = [
        'default_heading',
        'default_title'
    ];

    public function getDefaultTitleAttribute()
    {
        foreach ($this->title as $value) {
            return $value;
        }
        return '';
    }

    public function getDefaultHeadingAttribute()
    {
        if (is_array($this->heading)) {
            foreach ($this->heading as $value) {
                return $value;
            }
        }
        return '';
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
}
