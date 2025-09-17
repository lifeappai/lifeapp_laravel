<?php

namespace App\Models;

use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Mission mission
 */
class UserMissionComplete extends Model implements Coinable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'user_id',
        'rating',
        'mission_type',
        'earn_points',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return $this->mission->getCoinableType();
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return $this->mission->getCoinableObject();
    }
}
