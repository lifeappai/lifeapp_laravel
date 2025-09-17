<?php

namespace App\Models;

use App\Http\Traits\MovieTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Levels extends Model
{
    use HasFactory, MovieTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'subject_id',
        'level',
        'flag',
        'description',
        'img_name',
        'total_rewards',
        'total_question',
    ];
    /**
     * @return HasMany
     */
    public function subjects() : HasMany
    {
        return $this->hasMany(Subjects::class,'id','subject_id');
    }
     /**
     * @return HasMany
     */
    public function topics() : HasMany
    {
        return $this->hasMany(Topics::class,'level_id','id');
    }

    public function levelUnlock($user_id, $level_id)
    {
        $movieIds = $this->movieIds($user_id);
        $topicCountForLevel = Topics::where(['level_id' =>  $level_id])->count();
        $unlockTopics = Movie::whereIn('movies.id', $movieIds)->join('topics' ,'topics.id', 'movies.topic_id')->count();
        if($topicCountForLevel === $unlockTopics && $topicCountForLevel != 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isActiveForUser($user_id)
    {
        $levels = Levels::where('subject_id', $this->subject_id)->get();

        $completedLevels = [];
        foreach ($levels as $level) {
            $topics = $level->topics()->pluck('id');

            $completedTopic = Movie::whereIn('topic_id', $topics)
                ->whereHas('completed', function ($movieCompleted) use ($user_id) {
                    return $movieCompleted->where('user_id', $user_id);
                })->pluck('topic_id');

            if ($topics->diff($completedTopic)->count() == 0) {
                $completedLevels[] = $level->id;
            }
        }

        if (in_array($this->id, $completedLevels)) {
            return true;
        }

        $level = Levels::where('subject_id', $this->subject_id)->whereNotIn('id', $completedLevels)->orderBy('id', 'ASC')->first();
        return $level->id == $this->id;
    }

    /**
     * @return HasMany
     */
    public function level_translations() : HasMany
    {
        return $this->hasMany(LevelTranslation::class,'level_id');
    }
}
