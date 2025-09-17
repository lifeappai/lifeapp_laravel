<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Options extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function questions() : HasMany
    {
        return $this->hasMany(Questions::class,'id','question_id');
    }
  
}
