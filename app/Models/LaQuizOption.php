<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $default_text
 * @property $text
 * @property $la_quiz_question_id
 */
class LaQuizOption extends Model
{
    use HasFactory;

    protected $table = "la_quiz_options";

    protected $casts = [
        "text" => "array",
    ];

    protected $fillable = [
        "default_text",
        "text",
    ];
}
