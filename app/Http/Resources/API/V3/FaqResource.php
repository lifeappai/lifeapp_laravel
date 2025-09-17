<?php

namespace App\Http\Resources\API\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'question'  => $this->question,
            'answer'    => $this->answer,
            'media_id'  => $this->media_id,
            'audience' => $this->audience,
            'category'  => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'updated_at' => $this->updated_at
                ? $this->updated_at->timezone(config('app.timezone'))->toDateTimeString()
                : null,
        ];
    }
}
