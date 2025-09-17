<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class QuizResource extends JsonResource
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
            'movie_id' => $this->movie_id,
            'locale' => $this->locale,
            'brain_points' => $this->brain_points,
            'heart_points' => $this->heart_points,
            'question' => QuizQuestionResource::collection($this->quizQuestions),
		];
    }
}