<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_no',
        'type',
        'request_id',
        'status',
        'veified_at',
    ];
}
