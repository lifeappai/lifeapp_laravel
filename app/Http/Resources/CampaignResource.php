<?php

namespace App\Http\Resources;

use App\Models\Students;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Media;

class CampaignResource extends JsonResource
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
            'user_id' => $this->user_id,
            'how_coin' => $this->coins ?  $this->coins : 0,
            'title' => $this->title,
            'reason' => $this->reason,
            'coupon_id' => new CouponResource($this->coupon),
            'friends' => SearchFriendResource::collection(
                Students::whereIn('user_id', $this->friend_request)->get()
            )
		];
    }
}
