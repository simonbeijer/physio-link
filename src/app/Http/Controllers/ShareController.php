<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Models\Schema;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SchemaExerciseResource;

class ShareController extends Controller
{
    public function index(string $share_token): JsonResponse
    {
        $schema = Schema::where('share_token', $share_token)->first();

        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $exercises = $schema->exercises()->with('exercise')->orderBy('order')->get();

        if (!$exercises) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['exercises' => SchemaExerciseResource::collection($exercises)]);

    }
}
