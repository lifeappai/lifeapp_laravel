<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class BrainQuestion extends Model
{
    use HasFactory, Notifiable;
/**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'assign_rating_id',
        'attempt',
        'attempt_point',
    ];
   
}
