<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaFaqCategory extends Model
{
    use HasFactory;

    protected $table = 'la_faq_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function faqs()
    {
        return $this->hasMany(LaFaq::class, 'category_id');
    }
}
