<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questions extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function quiz() : HasMany
    {
        return $this->hasMany(Quiz::class,'id','quiz_id');
    }
     /**
     * @return HasMany
     */
    public function options() : HasMany
    {
        return $this->hasMany(Options::class,'question_id','id');
    }
}
