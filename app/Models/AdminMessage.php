<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMessage extends Model
{
    protected $fillable = ['title', 'body', 'user_ids', 'sent_at'];

    protected $casts = [
        'user_ids' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'admin_message_user');
    }

}
