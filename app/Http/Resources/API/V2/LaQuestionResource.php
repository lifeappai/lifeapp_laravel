<?php

namespace App\Http\Resources\API\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class LaQuestionResource extends JsonResource
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
            "subject" => $this->subject ? new LaSubjectResource($this->subject) : [],
            "level" => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            "title" => $this->title,
            "questions" => $this->questionOptions ? LaQuestionOptionResource::collection($this->questionOptions) : [],
            "answer" => $this->questionAnswer ? new LaQuestionOptionResource($this->questionAnswer) : [],
        ];
    }
}
