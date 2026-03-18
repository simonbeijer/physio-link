<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExerciseRequest;
use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::all();
        return response()->json(["exercises" => $exercises]);
    }

    public function store(StoreExerciseRequest $request)
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
