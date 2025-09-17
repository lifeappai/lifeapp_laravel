<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\CouponResource;
use App\Http\Resources\PublicUserResrouce;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'reason' => $this->reason,
            'coins' => $this->coins,
            'received_coins' => $this->givenCoins()->sum('coins'),
            'coupon' => $this->coupon ? new CouponResource($this->coupon) : null,
        ];

        if ($this->relationLoaded('users')) {
            $data['users'] = PublicUserResrouce::collection($this->users);
        }

        if ($this->relationLoaded('createdBy')) {
            $data['created_by'] = new PublicUserResrouce($this->createdBy);
        }

        $data['completed_at'] = $this->completed_at;
        $data['created_at'] = $this->created_at;
        $data['updated_at'] = $this->updated_at;

        return $data;
    }
}
