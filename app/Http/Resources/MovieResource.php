<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class MovieResource extends JsonResource
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
            'topic_id' => $this->topic_id,
            'title' => $this->title,
            'locale' => $this->locale,
            'media' => new MediaResource($this->media),
            'duration' => $this->duration,
            'after_duration' => $this->after_duration,
            'movie_type' => $this->movie_type,
            'brain_points' => $this->brain_points,
            'heart_points' => $this->heart_points,
		];
    }
}
