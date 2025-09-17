<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LaConceptCartoonHeaderResource extends JsonResource
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
            'heading' => $this->heading,
            'description' => $this->description,
            'button_one_text' => $this->button_one_text,
            'button_one_link' => $this->button_one_link,
            'button_two_text' => $this->button_two_text,
            'button_two_link' => $this->button_two_link,
            'document' => $this->media ? new MediaResource($this->media) : [],
        ];
    }
}
