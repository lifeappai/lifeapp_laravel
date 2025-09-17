<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaConceptCartoonHeader extends Model
{
    use HasFactory;
    protected $fillable = [
        'heading',
        'description',
        'button_one_text',
        'button_one_link',
        'button_two_text',
        'button_two_link',
        'media_id',
    ];

    
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
