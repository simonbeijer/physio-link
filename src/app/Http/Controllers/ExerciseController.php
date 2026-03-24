<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreExerciseRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $exercises = Exercise::all();
        return response()->json(["exercises" => $exercises]);
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {

        $exercise = Exercise::create([
            'name' => $request->name,
            'youtube_url' => $request->youtube_url,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'timer_duration' => $request->timer_duration
        ]);
        return response()->json($exercise, 201);

    }
}
