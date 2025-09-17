<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class LaQuizGameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "time" => $this->time,
            "game_code" => $this->game_code,
            "subject" => $this->laSubject ? new LaSubjectResource($this->laSubject) : [],
            "level" => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            "topic" => $this->laTopic ? new LaTopicResource($this->laTopic) : [],
            "participants" => $this->quizGameParticipants ? LaQuizGameParticipantResource::collection($this->quizGameParticipants) : [],
            "coins" => $this->getUserQuizGameCoins ? $this->getUserQuizGameCoins->coins : 0,
            "created_at" => $this->created_at,
            "status" => $this->status,
        ];
    }
}
