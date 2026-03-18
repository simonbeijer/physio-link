<?php

use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\SchemaExerciseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchemaController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->get('/schemas', [SchemaController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/schemas', [SchemaController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/exercises', [ExerciseController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/exercises', [ExerciseController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/schemas/{schema_id}/exercises', [SchemaExerciseController::class, 'store']);

require __DIR__.'/auth.php';