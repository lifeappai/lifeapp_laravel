<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisionQuestion extends Model
{
    use HasFactory;

    public function vision()
    {
        return $this->belongsTo(Vision::class);
    }

    public function answers()
    {
        return $this->hasMany(VisionQuestionAnswer::class);
    }
}
