<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class LaTeacherGradeResource extends JsonResource
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
            "grade" => $this->laGrade ? new LaGradeResource($this->laGrade) : [],
            "subject" => $this->laSubject ? new LaSubjectResource($this->laSubject) : [],
            "section" => $this->laSection ? new LaSectionResource($this->laSection) : [],
        ];
    }
}
