<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreSchemaRequest;
use Illuminate\Http\Request;

class SchemaController extends Controller
{
    public function index()
    {
        $schemas = auth()->user()->schemas;
        return response()->json(["schemas" => $schemas]);
    }

    public function store(StoreSchemaRequest $request) {
        $schema = auth()->user()->schemas()->create([
            "name" => $request->name,
            'share_token' => \Str::uuid()
        ]);
        return response()->json($schema, 201);
    }
}
