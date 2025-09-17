<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisionUserStatus extends Model
{
    protected $fillable = [
        'user_id',
        'vision_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vision()
    {
        return $this->belongsTo(Vision::class);
    }
}
