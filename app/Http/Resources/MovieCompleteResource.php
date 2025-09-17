<?php

namespace App\Http\Resources;

use App\Http\Resources\API\V1\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;


class MovieCompleteResource extends JsonResource
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
            'earn_point' => $this->earn_points,
            'rating' => $this->avg_rating,
		];
    }
}
