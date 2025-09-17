<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'id'  => $this->id,
            'school_id'  => $this->school_id,
            'name'  => $this->name,
            'username'  => $this->username,
            'mobile_no'  => $this->mobile_no,
            'dob'  => $this->dob,
            'grade'  => $this->grade,
            'state'  => $this->state,
            'address'  => $this->address,
            'image_path'  => $this->image_path,
            'profile_image'  => $this->profile_image,
            'token' => $this->accessToken,
        ];
    }
}
