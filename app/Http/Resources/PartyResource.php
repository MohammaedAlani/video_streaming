<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_url' => env('APP_URL') . $this->video()->first()->path,
            'video_image_url' => env('APP_URL') . $this->video()->first()->image,
            'host' => $this->user()->first(),
            'current_video_time' => $this->current_video_time,
            'status' => $this->status,
        ];
    }
}
