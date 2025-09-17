<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class MentorSubjectResource extends JsonResource
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
            'subject' => $this->laSubject ? new LaSubjectResource($this->laSubject) : "",
        ];
    }
}
