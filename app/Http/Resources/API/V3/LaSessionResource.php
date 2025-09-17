<?php

namespace App\Http\Resources\API\V3;

use App\Http\Resources\PublicUserResrouce;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LaSessionResource extends JsonResource
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
            "id" => $this->id,
            "heading" => $this->heading,
            "description" => $this->description,
            'user' => $this->user ? new PublicUserResrouce($this->user) : [],
            "zoom_link" => $this->zoom_link,
            "zoom_password" => $this->zoom_password,
            "date" => date("Y-m-d", strtotime($this->date_time)),
            "time" => date("H:i:s", strtotime($this->date_time)),
            "is_booked" => $this->checkSessionParticipant(Auth::user()->id) ? "1" : "0",
            "status" => $this->status ?? "0",
        ];
    }
}
