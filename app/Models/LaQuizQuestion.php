<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $created_by
 * @property $lq_quiz_id
 * @property $default_text
 * @property $text
 * @property $type
 */
class LaQuizQuestion extends Model
{
    use HasFactory;

    protected $table = "la_quiz_questions";

    const TYPE_MCQs = 0;

    protected $casts = [
        "text" => "array"
    ];

    protected $fillable = [
        "default_text",
        "text",
        "type"
    ];

    public function quiz()
    {
        return $this->belongsTo(LaQuiz::class, 'la_quiz_id');
    }

    public function options()
    {
        return $this->hasMany(LaQuizOption::class, 'la_quiz_question_id');
    }
}
