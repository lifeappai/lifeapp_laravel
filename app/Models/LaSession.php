<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'heading',
        'description',
        'date_time',
        'zoom_link',
        'zoom_password',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laSessionParticipant()
    {
        return $this->hasMany(LaSessionParticipant::class, 'la_session_id');
    }

    public function checkSessionParticipant($userId)
    {
        return LaSessionParticipant::where('user_id',$userId)->where('la_session_id',$this->id)->first();
    }
}