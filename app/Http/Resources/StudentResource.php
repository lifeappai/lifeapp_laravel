<?php

namespace App\Http\Resources;

use App\Models\School;
use Illuminate\Http\Resources\Json\JsonResource;


class StudentResource extends JsonResource
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
            'school' => new SchoolResource(School::where(['id' => $this->school_id])->first()),
            'school_id' => $this->school_id,
            'name' => $this->name,
            'username' => $this->username,
            'mobile_no' => $this->mobile_no,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'grade' => $this->grade,
            'city' => $this->city,
            'state' => $this->state,
            'address' => $this->address,
            'profile_image' => $this->image_path,
		];
    }
}
