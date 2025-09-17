<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LaConceptCartoonResource extends JsonResource
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
            "level" => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            "subject" => $this->subject ? new LaSubjectResource($this->subject) : [],
            'title' => $this->title,
            'document' => $this->media ? new MediaResource($this->media) : [],
        ];
    }
}
