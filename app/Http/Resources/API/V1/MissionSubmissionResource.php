<?php

namespace App\Http\Resources\API\V1;

use App\Models\Media;
use App\Models\UserMissionComplete;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $request->user();
        $upload = $user->missionUploads()->where('mission_id', $this->mission_id)->latest()->first();

        return [
            'id' => $this->id,
            'media' => $upload ? new \App\Http\Resources\MediaResource(Media::find($upload->question_media_id)) : null,
            'description' => $this->description,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'mission_points' => UserMissionComplete::where([
                'user_id' => $this->user_id,
                'mission_id' => $this->mission_id])->first(),
        ];
    }
}
