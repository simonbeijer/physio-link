<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Http\Requests\StoreSchemaRequest;
use App\Http\Resources\SchemaResource;
use Illuminate\Http\JsonResponse;

class SchemaController extends Controller
{
    public function index(): JsonResponse
    {
        $schemas = auth()->user()->schemas;
        return response()->json(["schemas" => SchemaResource::collection($schemas)]);
    }

    public function store(StoreSchemaRequest $request): JsonResponse
    {
        $schema = auth()->user()->schemas()->create([
            "name" => $request->name,
            'share_token' => \Str::uuid()
        ]);
        return response()->json(new SchemaResource($schema), 201);
    }

    public function destroy(int $id)
    {

        $schema = auth()->user()->schemas()->find($id);
        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $schema->delete();

        return response()->json(["message" => "deleted"], 200);

    }
}
