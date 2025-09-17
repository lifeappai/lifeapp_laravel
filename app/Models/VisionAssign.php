<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisionAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        "teacher_id",
        "student_id",
        "vision_id",
        "due_date",
        "type",
    ];

     /**
    * @return BelongsTo
    */

    public function vision()
    {
        return $this->belongsTo(Vision::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
