<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Web\LevelResource;
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
            'id' => $this->id,
            'locale_data' => $this->subject_translation,
            'levels' => LevelResource::collection($this->levels)
		];
    }
}
