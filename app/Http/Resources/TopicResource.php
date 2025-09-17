<?php

namespace App\Http\Resources;

use App\Http\Traits\MovieTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TopicResource extends JsonResource
{
    use MovieTrait;
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
            'title' => $this->title,
            'flag' => (int)$this->isActiveForUser(Auth::id()),
            'description' => $this->description,
            'media' => new MediaResource($this->media),
		];
    }
}
