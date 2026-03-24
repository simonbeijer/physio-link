<?php

namespace App\Http\Controllers;
use App\Models\Schema;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function index($share_token)
    {
        $schema = Schema::where('share_token', $share_token)->first();

        if (!$schema) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $exercises = $schema->exercises()->with('exercise')->orderBy('order')->get();

        if (!$exercises) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['exercises' => $exercises]);

    }
}
