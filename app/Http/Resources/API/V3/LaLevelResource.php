<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LaLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = Auth::user();
        return [
            "id" => $this->id,
            "title" => $this->title['en'] ?? null,
            "description" => $this->description['en'] ?? null,
            "vision_text_image_points" => $this->vision_text_image_points,
            "vision_mcq_points" => $this->vision_mcq_points,
            "mission_points" => $this->mission_points,
            "quiz_points" => $this->quiz_points,
            "riddle_points" => $this->riddle_points,
            "puzzle_points" => $this->puzzle_points,
            "jigyasa_points" => $this->jigyasa_points,
            "pragya_points" => $this->pragya_points,
            "teacher_assign_points" => $this->teacher_assign_points,
            "teacher_correct_submission_points" => $this->teacher_correct_submission_points,
            "quiz_time" => $this->quiz_time,
            "riddle_time" => $this->riddle_time,
            "puzzle_time" => $this->puzzle_time,
            "unlock" => $user ? ($user->laGrade ? $this->checkGradeUnlock($user->laGrade->name) : 0) : 0,
        ];
    }
}
