<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaLevel extends Model
{
    use HasFactory;

    public $fillable = [
        "title",
        "description",
        "vision_text_image_points",
        "vision_mcq_points", 
        'mission_points',
        "quiz_points",
        "riddle_points",
        "puzzle_points",
        "jigyasa_points",
        "pragya_points",
        "quiz_time",
        "riddle_time",
        "puzzle_time",
        "teacher_assign_points",
        "teacher_correct_submission_points",
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

    protected $appends = [
        'default_description',
        'default_title'
    ];

    public function getDefaultTitleAttribute()
    {
        foreach ($this->title as $value) {
            return $value;
        }
        return '';
    }

    public function getDefaulDescriptionAttribute()
    {
        foreach ($this->title as $value) {
            return $value;
        }
        return '';
    }

    public function checkGradeUnlock($grade)
    {
        return 1;
    //     $title = strtolower($this->title['en']);
    //     if ($grade >= 1 && $grade <= 5) {
    //         if ($title == "level 1") {
    //             return 1;
    //         } else {
    //             return 0;
    //         }
    //     } else if ($grade == 6) {
    //         if ($title == "level 1" || $title == "level 2") {
    //             return 1;
    //         } else {
    //             return 0;
    //         }
    //     } else if ($grade == 7) {
    //         if ($title == "level 1" || $title == "level 2" || $title == "level 3") {
    //             return 1;
    //         } else {
    //             return 0;
    //         }
    //     } else if ($grade  >= 8 && $grade <= 10) {
    //         return 1;
    //     }
    //     return 0;
    }
}
