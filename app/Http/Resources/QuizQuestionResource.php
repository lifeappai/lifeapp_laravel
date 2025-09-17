<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class QuizQuestionResource extends JsonResource
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
            'quiz_id' => $this->quiz_id,
            'locale' => $this->locale,
            'title' => $this->title,
            'type' => $this->type,
            'audio_media_id' => new MediaResource($this->audioMedia),
            'question_media_id' => new MediaResource($this->questionMedia),
            'answer' => $this->answer,
            'options' => QuestionOptionResource::collection($this->options),
		];
    }
}