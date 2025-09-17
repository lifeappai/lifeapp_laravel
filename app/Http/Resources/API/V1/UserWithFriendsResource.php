<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\PublicUserResrouce;
use App\Http\Resources\SchoolResource;
use App\Models\Friendship;

class UserWithFriendsResource extends PublicUserResrouce
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response  = parent::toArray($request);

        $user = $request->user();
        $friendRequest = Friendship::where(function ($query) use ($user) {
            $query->where('sender_id', $this->id)->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->where('recipient_id', $this->id);
        })->latest()->first();

        $response['friend_request'] = null;

        if ($friendRequest) {
            $response['friend_request'] =  [
                'id' => $friendRequest->id,
                'sender_id' => $friendRequest->sender_id,
                'recipient_id' => $friendRequest->recipient_id,
                'status' => $friendRequest->status,
                'created_at' => $friendRequest->created_at,
            ];
        }

        return $response;
    }
}
