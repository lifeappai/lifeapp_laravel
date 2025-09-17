<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Media;
use App\Models\User;

class HallOfFrameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::whereIn("mobile_no", [
            9969949630,
            7350295766,
            9969949630,
            8390071915,
            7745857864,
            7875614227,
            9324628212,
            8551919293,
            9491084185,
            7745857864
        ])->inRandomOrder()->first();

        return [
            'total_point' => $this->points,
            'user' => new PublicUserResrouce($user),
		];
    }
}
