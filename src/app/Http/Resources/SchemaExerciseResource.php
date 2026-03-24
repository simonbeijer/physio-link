<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchemaExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'schema_id' => $this->schema_id,
            'exercise_id' => $this->exercise_id,
            'order' => $this->order,
            'comfort_level' => $this->comfort_level,
            'exercise' => new ExerciseResource($this->whenLoaded('exercise')),
        ];
    }
}
