<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\PublicUserResrouce;

class FriendRequestResource extends PublicUserResrouce
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response =  parent::toArray($request);

        $response['fiend_request'] = null;

        if ($this->pivot) {
            $response['fiend_request'] = [
                'id' => $this->pivot->id,
                'sender_id' => $this->pivot->sender_id,
                'recipient_id' => $this->pivot->recipient_id,
                'status' => $this->pivot->status,
                'created_at' => $this->pivot->created_at,
            ];
        }

        return $response;
    }
}
