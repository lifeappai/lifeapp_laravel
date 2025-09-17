<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->id,
            'name' => $this->name,
            'grade' => $this->grade,
            'address' => $this->address,
            'topic_completed' => $this->topic_completed ?? 0,
            'mission_completed' => $this->mission_completed ?? 0,
            'school' => null,
            'mission_submit' => null,
        ];

        $school = json_decode($this->school);
        if($school && $school->id) {
            $res['school'] = $school;
        }

        $mission = json_decode($this->mission_submit);
        if($mission && $mission->id) {
            $res['mission_submit'] = [
                'id' => $mission->id,
                'name' => json_decode($mission->name),
            ];
        }

        return $res;
    }
}
