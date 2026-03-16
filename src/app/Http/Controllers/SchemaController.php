<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchemaController extends Controller
{
    public function index()
    {
        $schema = auth()->user()->schemas;
        return response()->json(["schema" => $schema]);
    }
}
