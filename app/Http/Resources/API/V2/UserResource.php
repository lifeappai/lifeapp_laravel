<?php

namespace App\Http\Resources\API\V2;

use App\Http\Resources\SchoolResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->image_path) {
            $media = $this->image_path;
        }
        $user = [
            'id' => $this->id ?? null,
            'student_id' => $this->student_id ?? null,
            'school' => $this->school ? new SchoolResource($this->school) : null,
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
            'mission_completes' => $this->laMissionApproved->count(),
            'quiz' => $this->laQuizGameResults->count(),
            'friends' => $this->friends->count() + $this->friendsAccept->count(),
            'earn_coins' => $this->earn_coins,
            'subjects' => (count($this->mentorSubjects) > 0) ? MentorSubjectResource::collection($this->mentorSubjects) : null,
        ];
        $token = $this->accessToken ? ['token' => $this->accessToken] : null;
        if (!empty($token)) {
            $user = array_merge($user, $token);
        }
        $userRank = $this->userRank ? ['user_rank' => $this->userRank] : null;
        if (!empty($userRank)) {
            $user = array_merge($user, $userRank);
        }
        $last_mission = $this->last_mission ? ['last_mission' => $this->last_mission] :  ['last_mission' => null];
        $user = array_merge($user, $last_mission);
        $missions = $this->missions ? ['missions' => $this->missions] : ['missions' => []];
        $user = array_merge($user, $missions);

        return $user;
    }
}
