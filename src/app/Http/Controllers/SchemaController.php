<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Http\Requests\StoreSchemaRequest;
use Illuminate\Http\JsonResponse;

class SchemaController extends Controller
{
    public function index(): JsonResponse
    {
        $schemas = auth()->user()->schemas;
        return response()->json(["schemas" => $schemas]);
    }

    public function store(StoreSchemaRequest $request): JsonResponse {
        $schema = auth()->user()->schemas()->create([
            "name" => $request->name,
            'share_token' => \Str::uuid()
        ]);
        return response()->json($schema, 201);
    }
}
