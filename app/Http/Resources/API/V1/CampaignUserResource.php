<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignUserResource extends JsonResource
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
            'username' => $this->username,
            'profile_image' => $this->image_path,
            'coins' => $this->pivot->coins,
            'given_at' => $this->pivot->given_at,
        ];
    }
}
