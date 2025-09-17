<?php

namespace App\Models;

use App\Http\Traits\MovieTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topics extends Model
{
    use HasFactory, Notifiable, MovieTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'level_id',
        'title',
        'flag',
        'description',
        'topic_media_id'
    ];
    /**
     * @return HasMany
     */
    public function levels() : HasMany
    {
        return $this->hasMany(Levels::class,'id','level_id');
    }
    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'topic_media_id');
    }

    public function topicComplete($user_id, $topicId)
    {
        $movieIds = $this->movieIds((int)$user_id);
        return Movie::where(["topic_id" => $topicId])->whereIn('id', $movieIds)->exists();
    }
    /**
     * @return HasMany
     */
    public function topic_translations() : HasMany
    {
        return $this->hasMany(TopicTranslation::class,'topic_id');
    }

    /**
     * @param $user_id
     */
    public function isActiveForUser($user_id)
    {
        $completedIds = self::getCompletedTopicIds((int)$user_id);
        if (in_array($this->id, $completedIds)) {
            return true;
        }
        $topic = Topics::where('level_id', $this->level_id)->whereNotIn('id', $completedIds)->orderBy('id', 'ASC')->first();
        return $topic->id == $this->id;
    }
}
