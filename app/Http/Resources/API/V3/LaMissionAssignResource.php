<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\PublicUserResrouce;
use App\Models\LaMissionComplete;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\MediaResource;

class LaMissionAssignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $missionComplete = LaMissionComplete::where('user_id', $this->user_id)->where('la_mission_id', $this->la_mission_id)
            ->where('status', '!=', 'skipped')
            ->whereHas('laMissionAssign', function($query) {
                $query->where('teacher_id', $this->teacher_id);
            })
            ->latest()
            ->first();

        $missionData = [
            "id" => $this->id,
            "teacher" =>  $this->teacher ? new PublicUserResrouce($this->teacher): [],
            'user' => $this->user ? new PublicUserResrouce($this->user) : [],
            'la_mission' => $this->laMission ? $this->laMission : [],
            "due_date" => $this->due_date ? date("d-m-Y", strtotime($this->due_date)) : null,
            "type" => $this->type ?? null,
            'submission' => $missionComplete ? new LaMissionCompleteResource($missionComplete) : null,
        ];

        return $missionData;
    }
}