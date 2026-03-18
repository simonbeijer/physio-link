<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchemaExercise;
use App\Http\Requests\StoreSchemaExerciseRequest;

class SchemaExerciseController extends Controller
{
    public function index($schema_id)
    {


        $exercises = SchemaExercise::where('schema_id', $schema_id)
            ->orderBy('order')
            ->get();

        return response()->json(["exercises" => $exercises]);

    }
    public function store(StoreSchemaExerciseRequest $request, $schema_id)
    {

        foreach ($request->exercises as $item) {
            SchemaExercise::create([
                "schema_id" => $schema_id,
                "exercise_id" => $item['exercise_id'],
                "order" => $item['order'],
                "comfort_level" => $item['comfort_level'] ?? null
            ]);
        };

        return response()->json(['message' => 'övning sparad'], 201);
    }
}
