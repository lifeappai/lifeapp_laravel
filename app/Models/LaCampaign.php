<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;


class LaCampaign extends Model
{
    protected $table = 'la_campaigns';

    protected $fillable = [
        'title',
        'description',
        'game_type',
        'reference_id',
        'scheduled_for',
        'la_subject_id',
        'la_level_id',
        'media_id',
        'button_name',
    ];

    public function vision()
    {
        return $this->belongsTo(Vision::class, 'reference_id', 'id')
                    ->where('type', 7);
    }

    public function mission()
    {
        return $this->belongsTo(LaMission::class, 'reference_id', 'id')
                    ->where('type', 1);
    }

    public function question()
    {
        return $this->belongsTo(LaQuestion::class, 'reference_id', 'id')
                    ->where('type', 2);
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
