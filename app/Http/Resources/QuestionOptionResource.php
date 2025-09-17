<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class QuestionOptionResource extends JsonResource
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
            'question_id' => $this->question_id,
            'locale' => $this->locale,
            'option_title' => $this->option_title,
            'media' => new MediaResource($this->media),
		];
    }
}