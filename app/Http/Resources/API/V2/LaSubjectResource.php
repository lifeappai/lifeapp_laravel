<?php

namespace App\Http\Resources\API\V2;

use App\Enums\StatusEnum;
use App\Models\LaSubjectCouponCode;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LaSubjectResource extends JsonResource
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
        if ($this->image) {
            foreach ($this->image as $key => $media) {
                $image[$key]  = $this->getMediaResource($media);
            }
        }

        $checkCouponCodeUnlock = 0;
        if (Auth::user()) {
            $couponCodeUnlock = LaSubjectCouponCode::where('user_id', Auth::user()->id)->where('la_subject_id', $this->id)->first();
            if ($couponCodeUnlock) {
                $checkCouponCodeUnlock = 1;
            }
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'heading' => $this->heading,
            'image' => $image,
            'is_coupon_available' => ($this->is_coupon_available == StatusEnum::ACTIVE) ? true : false,
            'coupon_code_unlock' => ($checkCouponCodeUnlock == StatusEnum::ACTIVE) ? true : false,
        ];
    }
}
