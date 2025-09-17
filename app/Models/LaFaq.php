<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaFaq extends Model
{
    use HasFactory;

    protected $table = 'la_faq';

    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'media_id',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(LaFaqCategory::class, 'category_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id'); // Assuming Media model exists
    }
}
