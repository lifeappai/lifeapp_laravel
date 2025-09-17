<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\API\V1\MediaResource;
use App\Http\Resources\PublicUserResrouce;
use Illuminate\Http\Resources\Json\JsonResource;

class LaMissionCompleteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $missionData = [
            "id" => $this->id,
            'user' => $this->user ? new PublicUserResrouce($this->user) : [],
            "mission_title" =>  $this->laMission->title['en'] ?? null,
            "media" => $this->media ? new MediaResource($this->media) : [],
            "description" => $this->description  ?? null,
            "comments" => $this->comments ?? null,
            "approved_at" => $this->approved_at ? date("Y-m-d H:i:s") : null,
            "rejected_at" => $this->rejected_at ? date("Y-m-d H:i:s") : null,
            "points" => $this->points ?? 0,
            "timing" => $this->timing ?? null,
        ];

        return $missionData;
    }
}
