<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->image_path) {
            $media = $this->image_path;
        }

        return [
            'id' => $this->user_id,
            'name' => $this->name,
            'url' => $media ?? null,
        ];
    }
}
