<?php

use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\SchemaExerciseController;
use App\Http\Controllers\SchemaController;
use App\Http\Controllers\ShareController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    Route::get('/schemas', [SchemaController::class, 'index']);
    Route::post('/schemas', [SchemaController::class, 'store']);
    Route::delete('/schemas/{schema_id}', [SchemaController::class, 'destroy']);
    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::patch('/exercises/{exercise_id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{exercise_id}', [ExerciseController::class, 'destroy']);
    Route::get('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'index']);
    Route::post('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'store']);
});

Route::middleware('throttle:30,1')
    ->get('/share/{share_token}', [ShareController::class, 'index']);

require __DIR__ . '/auth.php';