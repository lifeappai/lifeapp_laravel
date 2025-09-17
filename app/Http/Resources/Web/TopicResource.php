<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TopicResource extends JsonResource
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
            'level_id' => $this->level_id,
            'local_data' => $this->topic_translations,
            // 'title' => $this->title,
            // 'flag' => $this->topicComplete(Auth::id(), $this->id)? 1 : 0,
            // 'description' => $this->description,
            'media' => new MediaResource($this->media),
		];
    }
}
