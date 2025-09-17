<?php

namespace App\Http\Traits;
use App\Models\Movie;
use App\Models\Levels;
use App\Models\MovieComplete;
use App\Models\Topics;

trait MovieTrait {
    public function movieIds(int $user_id)
    {
        $movieId = MovieComplete::select('movie_id')->where(['user_id' => $user_id])->get();
        $mv = array();
        foreach($movieId as $movie){
           $mv[] = $movie->movie_id;
        }
        return $mv;
    }
    public function firstTopic(int $level_id)
    {
        $topic = Topics::where(['level_id' => $level_id])->first();
        return $topic->id;
    }

    public function firstLevel(int $subject_id)
    {
        $level = Levels::where(['subject_id' => $subject_id])->first();
        return $level->id;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getCompletedTopicIds($user_id)
    {
        $topicsIds = Movie::select('topic_id')
            ->wherehas('completed', function ($moveComplete) use ($user_id) {
                return $moveComplete->where('user_id', $user_id);
            })->pluck('topic_id')->toArray();

        return array_values($topicsIds);
    }
}
