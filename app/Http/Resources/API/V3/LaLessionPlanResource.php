<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\API\V1\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LaLessionPlanResource extends JsonResource
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
            "language" => $this->laLessionPlanLanguage ? new LaLessionPlanLanguageResource($this->laLessionPlanLanguage) : [],
            "board" => $this->laBoard ? new LaBoardResource($this->laBoard) : [],
            'title' => $this->title,
            'document' => $this->media ? new MediaResource($this->media) : [],
        ];
    }
}
