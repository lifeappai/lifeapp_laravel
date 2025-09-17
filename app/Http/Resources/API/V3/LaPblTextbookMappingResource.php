<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\API\V1\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LaPblTextbookMappingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'language' => $this->language ? new LaLessionPlanLanguageResource($this->language) : [],
            'board'    => $this->laBoard ? new LaBoardResource($this->laBoard) : [],
            'subject'  => $this->laSubject ? new LaSubjectResource($this->laSubject) : [],
            'grade'    => $this->laGrade ? new LaGradeResource($this->laGrade) : [],
            'title'    => $this->title,
            'document' => $this->media ? new MediaResource($this->media) : [],
        ];
    }
}
