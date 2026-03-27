<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'name' => $this->name,
            'youtube_url' => $this->youtube_url,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'timer_duration' => $this->timer_duration
        ];
    }
}
