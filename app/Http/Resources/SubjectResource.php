<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class SubjectResource extends JsonResource
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
            'id' => $this->subject_id,
            'title' => $this->title,
            'locale' => $this->locale,
            'levels' => LevelResource::collection($this->levels($this->subject_id, $this->locale)),
		];
    }
}
