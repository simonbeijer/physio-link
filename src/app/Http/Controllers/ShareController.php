<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Models\Schema;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SchemaExerciseResource;
use App\Http\Resources\SchemaResource;

class ShareController extends Controller
{
    public function index(string $share_token): JsonResponse
    {
        $schema = Schema::where('share_token', $share_token)->first();

        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $exercises = $schema->exercises()->with('exercise')->orderBy('order')->get();

        return response()->json([
            'schema' => new SchemaResource($schema),
            'exercises' => SchemaExerciseResource::collection($exercises)
        ]);

    }
}
