<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\PublicUserResrouce;
use Illuminate\Http\Resources\Json\JsonResource;

class LaQuizGameResultResource extends JsonResource
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
            "id" => $this->id,
            'user' => $this->user ? new PublicUserResrouce($this->user) : [],
            "total_questions" => $this->total_questions,
            "total_correct_answers" => $this->total_correct_answers,
            "total_wrong_answers" => $this->total_questions - $this->total_correct_answers,
            "coins" => $this->coins,

        ];
    }
}
