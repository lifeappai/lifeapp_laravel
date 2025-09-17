<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $created_by
 * @property $level
 * @property $coins
 */
class LaQuiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "la_quizzes";

    const LEVEL_EASY = 0;

    const LEVEL_MODERATE = 1;

    const LEVEL_HARD = 2;

    const LEVEL_SUPER_HARD = 3;

    protected $fillable = [
        "level",
        "coins",
    ];

    public function questions()
    {
        return $this->hasMany(LaQuizQuestion::class, 'la_quiz_id');
    }
}
