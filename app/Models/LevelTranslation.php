<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class LevelTranslation extends Model
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'level_id',
        'locale',
        'description'
    ];
   
    // public function levels($subject_id, $locale)
    // {
    //     return Levels::where(['subject_id' =>  $subject_id, 'locale' =>  $locale])->get();
    // }
}
