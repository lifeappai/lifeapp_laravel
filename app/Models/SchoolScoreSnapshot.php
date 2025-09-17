<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GameType;
use App\Http\Resources\API\V1\MediaResource;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class SchoolScoreSnapshot extends Model
{
    protected $table = 'school_score_snapshots';

    protected $fillable = [
        'school_id',
        'month',
        's_score',
        'teacher_coins',
        'student_coins',
        'total_coins',
        'max_teacher_coins',
        'max_student_coins',
    ];

    public $timestamps = true;

    
    public function school()
    {
        return $this->belongsTo(User::class, 'school_id');
    }
}
