<?php

namespace App\Http\Resources;

use App\Models\CouponRedeem;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = Auth::user();

        $redeemed = CouponRedeem::where('user_id', $user->id)->where('coupon_id', $this->id)->exists();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'coin' => $this->coin,
            'link' => $redeemed ? $this->link : null,
            'details' => $this->details,
            'coupon_media_id' => new MediaResource($this->media),
            'redeemable' => $this->coin <= $user->brain_coins,
            'redeemed' => $redeemed,
		];
    }
}
