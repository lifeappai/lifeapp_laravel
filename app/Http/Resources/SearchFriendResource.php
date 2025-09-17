<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SearchFriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;

        $user = [
          'id'  => $user->id ,
          'name'  => $user->name ,
          'username'  => $user->username,
          'mobile_no'  => $user->mobile_no ,
          'dob'  => $user->dob,
          'state'  => $user->state,
          'address'  => $user->address,
          'profile_image'  => $user->image_path
        ];

        return $user;
    }
}
