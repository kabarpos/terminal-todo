<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    public function index()
    {
        return Inertia::render('Platforms/Index', [
            'platforms' => Platform::withCount('tasks')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($platform) => [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'slug' => $platform->slug,
                    'icon' => $platform->icon,
                    'description' => $platform->description,
                    'settings' => $platform->settings,
                    'is_active' => $platform->is_active,
                    'tasks_count' => $platform->tasks_count,
                    'created_at' => $platform->created_at->format('d M Y')
                ])
        ]);
    }

    public function create()
    {
        return Inertia::render('Platforms/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:platforms',
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        Platform::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'description' => $request->description,
            'settings' => $request->settings,
            'is_active' => true
        ]);

        return redirect()->route('platforms.index')
            ->with('message', 'Platform berhasil dibuat');
    }

    public function edit(Platform $platform)
    {
        return Inertia::render('Platforms/Edit', [
            'platform' => [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'description' => $platform->description,
                'settings' => $platform->settings,
                'is_active' => $platform->is_active
            ]
        ]);
    }

    public function update(Request $request, Platform $platform)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:platforms,name,' . $platform->id,
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $platform->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'description' => $request->description,
            'settings' => $request->settings,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('platforms.index')
            ->with('message', 'Platform berhasil diperbarui');
    }

    public function destroy(Platform $platform)
    {
        if ($platform->tasks()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus platform yang memiliki task');
        }
        
        $platform->delete();
        return redirect()->route('platforms.index')
            ->with('message', 'Platform berhasil dihapus');
    }
} 