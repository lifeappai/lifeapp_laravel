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

class TeacherScoreSnapshot extends Model
{
    protected $table = 'teacher_score_snapshots';

    protected $fillable = [
        'user_id',
        'month',
        't_score',
        'assign_task_coins',
        'correct_submission_coins',
        'max_possible_coins',
        'engagement_badge',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
