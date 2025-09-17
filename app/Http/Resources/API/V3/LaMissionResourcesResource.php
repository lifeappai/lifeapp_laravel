<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\API\V1\MediaResource;
use App\Models\Mission;
use Illuminate\Http\Resources\Json\JsonResource;

class LaMissionResourcesResource extends JsonResource
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
            "title" => $this->title,
            "media" => $this->media ? new MediaResource($this->media) : [],
            "locale" => $this->locale,
        ];
    }
}
