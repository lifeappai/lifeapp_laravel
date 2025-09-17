<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class SubjectTranslation extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'subject_id',
        'locale',
        'title'
    ];
   
    public function levels($subject_id, $locale)
    {
        return Levels::select('levels.*', 'level_translations.locale', 'level_translations.description')
        ->join('level_translations', 'levels.id', '=','level_translations.level_id')
        ->where(['levels.subject_id' =>  $subject_id])
        ->where(['level_translations.locale' =>  $locale])
        ->orderBy('levels.id', 'ASC')
        ->get();
    }
}
