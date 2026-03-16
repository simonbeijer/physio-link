<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchemaController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->get('/schemas', [SchemaController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/schemas', [SchemaController::class, 'store']);

require __DIR__.'/auth.php';