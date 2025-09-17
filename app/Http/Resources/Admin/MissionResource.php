<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $translations = [];

        if (is_array($this->mission_name)) {
            foreach ($this->mission_name as $locale => $name) {
                $translations[$locale] = [
                    'name' => $name
                ];
            }
        } else {
            $translations[$this->locale] = [
                'name' => $this->mission_name
            ];
        }

        foreach ($this->missionQuestions as $question) {
            $translations[$question->locale]['question'] = new MissionQuestionResource($question);
        }

        foreach ($this->missionImages as $image) {
            $translations[$image->locale]['images'][] = new MissionImageResource($image);
        }

        return [
            'id' => $this->id,
            'mission_type' => $this->mission_type,
            'brain_point' => $this->brain_points,
            'heart_point' => $this->heart_points,
            'translations' => $translations,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
