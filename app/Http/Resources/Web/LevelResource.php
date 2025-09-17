<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LevelResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'level' => $this->level,
            // 'flag' => $this->levelUnlock(Auth::id(), $this->id)? 1 : 0,
            // 'description' => $this->description,
            // 'locale' => $this->locale,
            'total_rewards' => $this->total_rewards,
            'total_question' => $this->total_question,
            'local_data' => $this->level_translations
		];
    }
}
