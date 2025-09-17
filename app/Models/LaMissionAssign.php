<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaMissionAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        "teacher_id",
        "user_id",
        "la_mission_id",
        "due_date",
        "type",
    ];

     /**
    * @return BelongsTo
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     /**
    * @return BelongsTo
    */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    /**
    * @return BelongsTo
    */
    public function laMission(): BelongsTo
    {
        return $this->belongsTo(LaMission::class, 'la_mission_id');
    }
}