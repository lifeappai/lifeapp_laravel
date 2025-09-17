<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class LaTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = [];
        if ($this->image) {
            foreach ($this->image as $key => $media) {
                $image[$key]  = $this->getMediaResource($media);
            }
        }
        return [
            "id" => $this->id,
            "title" => $this->title['en'] ?? null,
            'image' => $image['en'] ?? null,
        ];
    }
}
