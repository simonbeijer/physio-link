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

Route::middleware(['auth:sanctum'])->get('/schemas', [SchemaController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/schemas', [SchemaController::class, 'store']);

Route::middleware(['auth:sanctum'])->delete('/schemas/{schema_id}', [SchemaController::class, 'destroy']);

Route::middleware(['auth:sanctum'])->get('/exercises', [ExerciseController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/exercises', [ExerciseController::class, 'store']);

Route::middleware(['auth:sanctum'])->delete('/exercises/{exercise_id}', [ExerciseController::class, 'destroy']);

Route::middleware(['auth:sanctum'])->get('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'store']);

Route::get('/share/{share_token}', [ShareController::class, 'index']);

require __DIR__.'/auth.php';