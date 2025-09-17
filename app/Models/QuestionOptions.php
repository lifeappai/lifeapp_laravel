<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOptions extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'question_id',
        'locale',
        'option_title',
        'option_image_id',
    ];
     /**
     * @return HasMany
     */
    public function options() : HasMany
    {
        return $this->hasMany(QuizQuestion::class,'id','question_id');
    }
    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'option_image_id');
    }
   
}
