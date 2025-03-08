<?php

namespace App\Http\Controllers;

use App\Models\SocialPlatform;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SocialPlatformController extends Controller
{
    public function index()
    {
        $platforms = SocialPlatform::with(['accounts', 'metrics'])
            ->withCount(['accounts', 'metrics'])
            ->get();

        return Inertia::render('SocialPlatforms/Index', [
            'platforms' => $platforms
        ]);
    }

    public function create()
    {
        return Inertia::render('SocialPlatforms/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        SocialPlatform::create($validated);

        return redirect()->route('social-platforms.index')
            ->with('message', 'Platform berhasil ditambahkan');
    }

    public function show(SocialPlatform $platform)
    {
        $platform->load(['accounts', 'metrics']);
        
        return Inertia::render('SocialPlatforms/Show', [
            'platform' => $platform
        ]);
    }

    public function edit(SocialPlatform $platform)
    {
        return Inertia::render('SocialPlatforms/Edit', [
            'platform' => $platform
        ]);
    }

    public function update(Request $request, SocialPlatform $platform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $platform->update($validated);

        return redirect()->route('social-platforms.index')
            ->with('message', 'Platform berhasil diperbarui');
    }

    public function destroy(SocialPlatform $platform)
    {
        $platform->delete();

        return redirect()->route('social-platforms.index')
            ->with('message', 'Platform berhasil dihapus');
    }
} 