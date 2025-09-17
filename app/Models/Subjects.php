<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Subjects extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'flag'
    ];
    /**
     * @return HasMany
     */
    public function levels() : HasMany
    {
        return $this->hasMany(Levels::class,'subject_id');
    }

    /**
     * @return HasMany
     */
    public function subject_translation() : HasMany
    {
        return $this->hasMany(SubjectTranslation::class,'subject_id');
    }
}
