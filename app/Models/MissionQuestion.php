<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionQuestion extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'question_title',
        'question_image',
        'question_type'
    ];
    /**
     * @return HasMany
     */
    public function missions() : HasMany
    {
        return $this->hasMany(Mission::class,'id','quiz_id');
    }

}
