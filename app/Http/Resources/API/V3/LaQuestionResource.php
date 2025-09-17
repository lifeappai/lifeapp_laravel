<?php

namespace App\Http\Resources\API\V3;

use App\Enums\GameType;
use App\Http\Resources\API\V1\MediaResource;
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
        $title = $this->title['en'] ?? null;
        if ($title && $this->type == GameType::PUZZLE && $this->question_type == GameType::QUESTION_TYPE['IMAGE']) {
            $media = $this->getMedia($title);
            $title = new MediaResource($media);
        }
        return [
            "id" => $this->id,
            "subject" => $this->subject ? new LaSubjectResource($this->subject) : [],
            "level" => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            "topic" => $this->laTopic ? new LaTopicResource($this->laTopic) : [],
            "title" => $title,
            "questions" => $this->questionOptions ? LaQuestionOptionResource::collection($this->questionOptions) : [],
            "answer" => $this->questionAnswer ? new LaQuestionOptionResource($this->questionAnswer) : [],
            "question_type" => $this->question_type,
            "type" => $this->type,
        ];
    }
}
