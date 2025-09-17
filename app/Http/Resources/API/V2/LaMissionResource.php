<?php

namespace App\Http\Resources\API\V2;

use App\Http\Resources\API\V1\MediaResource;
use App\Models\LaMissionComplete;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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

        $missionComplete = LaMissionComplete::where('user_id', Auth::user()->id)->where('la_mission_id', $this->id)->latest()->first();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $image,
            'document' => count($document) > 0 ? $document : null,
            'question' => $this->question,
            "subject" => $this->subject ? new LaSubjectResource($this->subject) : [],
            'resources' => $this->laMissionResources ? LaMissionResourcesResource::collection($this->laMissionResources) : [],
            'submission' => $missionComplete ? new LaMissionCompleteResource($missionComplete) : null,
        ];
    }
}
