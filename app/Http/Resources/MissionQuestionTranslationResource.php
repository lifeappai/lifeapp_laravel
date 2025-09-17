<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Media;

class MissionQuestionTranslationResource extends JsonResource
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
            'id' => $this->id,
            'mission_id' => $this->mission_id,
            'locale' => $this->locale,
            'question_title' => $this->question_title,
            'media' => new MediaResource($this->media),
		];
    }
}
