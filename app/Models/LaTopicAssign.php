<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaTopicAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        "teacher_id",
        "user_id",
        "la_topic_id",
        "due_date",
        "type",
    ];
}
