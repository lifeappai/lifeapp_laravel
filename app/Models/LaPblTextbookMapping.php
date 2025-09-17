<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaPblTextbookMapping extends Model
{
    protected $fillable = [
        'la_board_id', 'language_id', 'la_subject_id', 'la_grade_id',
        'title', 'document_id', 'status'
    ];

    public function laBoard()
    {
        return $this->belongsTo(LaBoard::class, 'la_board_id');
    }

    public function language()
    {
        return $this->belongsTo(LaLessionPlanLanguage::class, 'language_id');
    }

    public function laSubject()
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laGrade()
    {
        return $this->belongsTo(LaGrade::class, 'la_grade_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'document_id');
    }
}
