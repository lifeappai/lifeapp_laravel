<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaSessionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_session_id",
        "user_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laSession()
    {
        return $this->belongsTo(LaSession::class, 'la_session_id');
    }
    
}