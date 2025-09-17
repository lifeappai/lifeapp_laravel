<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\API\V1\MediaResource;
use App\Models\LaMissionComplete;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;

class LaMissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = [];
        $document = [];

        if ($this->image) {
            foreach ($this->image as $key => $media) {
                $image[$key]  = $this->getMediaResource($media);
            }
        }

        if ($this->document) {
            foreach ($this->document as $key => $documentMedia) {
                $document[$key]  = $this->getMediaResource($documentMedia);
            }
        }

        // ✅ Check if student has already submitted
        $missionComplete = LaMissionComplete::where('user_id', Auth::id())
            ->where('la_mission_id', $this->id)
            ->latest()
            ->first();

        // ✅ Default status logic
        $status = $missionComplete ? $missionComplete->getRawOriginal('status') : 'Start';

        // ✅ Override status if teacher has assigned but no submission yet
        if (!$missionComplete) {
            $isAssigned = $this->laMissionAssigns()
                ->where('user_id', Auth::id())
                ->exists();

            if ($isAssigned) {
                $status = 'Teacher Assigned';
            }
        }

        return [
            'id' => $this->id,
            "level" => $this->laLevel ? new LaLevelResource($this->laLevel) : [],
            "topic" => $this->laTopic ? new LaTopicResource($this->laTopic) : [],
            "type" => $this->type,
            'title' => $this->title['en'] ?? null,
            'description' => $this->description['en'] ?? null,
            'image' => $image['en'] ?? null,
            'document' => $document['en'] ?? null,
            'question' => $this->question['en'] ?? null,
            "subject" => $this->subject ? new LaSubjectResource($this->subject) : [],
            'chapter' => $this->chapter ? [
                'id' => $this->chapter->id,
                'title' => $this->chapter->title,
            ] : null,
            'resources' => $this->laMissionResources ? LaMissionResourcesResource::collection($this->laMissionResources) : [],
            'submission' => $missionComplete ? new LaMissionCompleteResource($missionComplete) : null,
            'status' => $status,
            'assigned_by' => $this->laMissionAssigns()->exists() ? optional($this->laMissionAssigns()->first()->teacher)->name : null,
        ];
    }
}
