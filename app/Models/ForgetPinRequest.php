<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgetPinRequest extends Model
{
    use HasFactory;
    protected $table = "forget_pin_requests";

    protected $fillable = [
        'user_id',
        'mobile_no',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
