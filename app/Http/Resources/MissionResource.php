<?php

namespace App\Http\Resources;

use App\Http\Resources\API\V1\MissionSubmissionResource;
use App\Models\UserMissionComplete;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\MissionQuestionTranslation;
use Illuminate\Support\Facades\Auth;

class MissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locale = $request->has('locale') ? $request->get('locale') : "En";
        $user = Auth::user();

        $missionComplete = $user->missionCompletes()->where('mission_id', $this->id)->latest()->first();

        return [
            'id' => $this->id,
            'name' => $this->getLocaleName($locale),
            'type' => $this->mission_type,
            'locale' => $locale,
            'brain_point' => $this->brain_points,
            'heart_point' => $this->heart_points,
            'flag' => (int)$this->isActive($user),
            'question' => MissionQuestionTranslationResource::collection($this->missionQuestions()->where('locale', $locale)->get()),
            'mission_images' => MissionImageResource::collection($this->missionImages()->where('locale', $locale)->get()),
            'submission' => $missionComplete ? new MissionSubmissionResource($missionComplete) : null,
		];
    }
}
