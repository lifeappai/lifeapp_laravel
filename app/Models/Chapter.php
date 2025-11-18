<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $table = 'chapters'; // or 'la_chapters' if that’s the table name

    protected $fillable = [
        'la_board_id',
        'la_grade_id',
        'la_subject_id',
        'title',
        'description',
        'order_no',
    ];

    public function board()
    {
        return $this->belongsTo(LaBoard::class, 'la_board_id');
    }

    public function grade()
    {
        return $this->belongsTo(LaGrade::class, 'la_grade_id');
    }

    public function subject()
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function missions()
    {
        return $this->hasMany(LaMission::class, 'chapter_id');
    }

    public function visions()
    {
        return $this->belongsToMany(
            Vision::class,
            'vision_chapter',
            'chapter_id',
            'vision_id'
        );
    }
}
