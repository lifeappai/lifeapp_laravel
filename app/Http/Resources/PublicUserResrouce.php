<?php

namespace App\Http\Resources;

use App\Http\Resources\API\V3\LaSectionResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V3\LaGradeResource;

class PublicUserResrouce extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile_no' => $this->mobile_no,
            'username' => $this->username,
            'school' => new SchoolResource($this->school),
            'state' => $this->state,
            'profile_image' => $this->image_path,
            'section' => $this->laSection ? new LaSectionResource($this->laSection) : null,
            'grade'         => $this->laGrade ? new LaGradeResource($this->laGrade) : null,
        ];
    }
}
