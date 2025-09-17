<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\PublicUserResrouce;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LaQuizGameParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $participates = [
            'id' => $this->id,
            'status' => $this->status ?? 1,
            'user' => $this->user ? new PublicUserResrouce($this->user) : [],
            'created_at' => date("Y-m-d H:i:s", strtotime($this->created_at)),
        ];
        return $participates;
    }
}
