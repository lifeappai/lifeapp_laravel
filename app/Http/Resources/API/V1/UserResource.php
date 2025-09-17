<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\MissionResource;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\TopicResource;
use App\Models\Topics;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property array points
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  $request
     * @return mixed
     */
    public function toArray($request)
    {
        if ($this->image_path) {
            $media = $this->image_path;
        }

        $topic = $this->getActiveTopic();

        if ($topic) {
            $topic = Topics::select('topics.*', 'topic_translations.locale', 'topic_translations.title', 'topic_translations.description')
                ->join('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                ->where(['topic_translations.locale' => 'en'])
                ->where('topics.id', $topic->id)
                ->first();
        }

        $user = [
            'id' => $this->id ?? null,
            'student_id' => $this->student_id ?? null,
            'school' => new SchoolResource($this->school),
            'name' => $this->name ?? null,
            'username' => $this->username ?? null,
            'mobile_no' => $this->mobile_no ?? null,
            'dob' => $this->dob ?? null,
            'gender' => $this->gender,
            'grade' => $this->grade ?? null,
            'city' => $this->city,
            'state' => $this->state ?? null,
            'address' => $this->address ?? null,
            'image_path' => $media ?? null,
            'profile_image' => $this->profile_image ?? null,
            'mission_complete' => $this->missionCoutByUser($this->id) ?? 0,
            'movie_complete' => $this->movieCoutByUser($this->id) ?? 0,
            'active_topic' => new TopicResource($topic),
            'active_mission' => new MissionResource($this->getActiveMission()),
            'points' => $this->points,
        ];

        $token = $this->accessToken ? ['token' => $this->accessToken] : null;
        if (!empty($token)) {
            $user = array_merge($user, $token);
        }
        return $user;
    }
}
