<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::withCount('tasks')->get();
        return response()->json($platforms);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:platforms',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'requirements' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $platform = Platform::create($request->all());
        return response()->json($platform, 201);
    }

    public function show(Platform $platform)
    {
        $platform->load('tasks');
        return response()->json($platform);
    }

    public function update(Request $request, Platform $platform)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:platforms,name,' . $platform->id,
            'description' => 'nullable|string',
            'icon' => 'string|max:50',
            'requirements' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $platform->update($request->all());
        return response()->json($platform);
    }

    public function destroy(Platform $platform)
    {
        if ($platform->tasks()->count() > 0) {
            return response()->json(['error' => 'Cannot delete platform with associated tasks'], 422);
        }
        
        $platform->delete();
        return response()->json(null, 204);
    }
} 