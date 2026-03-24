<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchemaExerciseRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Schema;
use App\Http\Resources\SchemaExerciseResource;


class SchemaExerciseController extends Controller
{
    public function index(int $schema_id): JsonResponse
    {
        $schema = $this->verifyOwnership($schema_id);

        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $exercises = $schema->exercises()
            ->orderBy('order')
            ->get();

        return response()->json(["exercises" => SchemaExerciseResource::collection($exercises)]);

    }
    public function store(StoreSchemaExerciseRequest $request,int $schema_id): JsonResponse
    {
        $schema = $this->verifyOwnership($schema_id);

        if (!$schema) {
            return response()->json(['message' => ' Unauthorized'], 401);
        }

        $schema->exercises()->delete();

        foreach ($request->exercises as $item) {
            $schema->exercises()->create([
                "schema_id" => $schema_id,
                "exercise_id" => $item['exercise_id'],
                "order" => $item['order'],
                "comfort_level" => $item['comfort_level'] ?? null
            ]);
        }
        ;

        return response()->json(['message' => 'övning sparad'], 201);
    }

    private function verifyOwnership(int $schema_id): bool | Schema
    {

        $schema = auth()->user()->schemas()->find($schema_id);

        if (!$schema) {
            return false;
        }

        return $schema;
    }
}
