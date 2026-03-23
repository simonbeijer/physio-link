<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchemaExercise;
use App\Http\Requests\StoreSchemaExerciseRequest;

class SchemaExerciseController extends Controller
{
    public function index($schema_id)
    {
        $schema = $this->verifyOwnership($schema_id);

        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $exercises = $schema->exercises()
            ->orderBy('order')
            ->get();

        return response()->json(["exercises" => $exercises]);

    }
    public function store(StoreSchemaExerciseRequest $request, $schema_id)
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

    private function verifyOwnership($schema_id)
    {

        $schema = auth()->user()->schemas()->find($schema_id);

        if (!$schema) {
            return false;
        }

        return $schema;
    }
}
