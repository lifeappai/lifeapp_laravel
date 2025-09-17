<?php

namespace App\Http\Resources;

use App\Http\Traits\MovieTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LevelResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'level' => $this->level,
            'flag' => (int)$this->isActiveForUser(Auth::id()),
            'description' => $this->description,
            'locale' => $this->locale,
            'total_rewards' => $this->total_rewards,
            'total_question' => $this->total_question,
		];
    }
}
