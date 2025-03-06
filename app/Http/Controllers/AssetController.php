<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:view-asset')->only(['index']);
        $this->middleware('permission:manage-asset')->only(['store', 'destroy']);
    }

    public function index()
    {
        try {
            $assets = Asset::with('uploader')
                ->latest()
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'type' => $this->getAssetType($asset->mime_type),
                        'size' => $asset->file_size,
                        'url' => Storage::url($asset->file_path),
                        'created_at' => $asset->created_at->format('Y-m-d H:i:s'),
                        'uploader' => $asset->uploader ? [
                            'name' => $asset->uploader->name,
                            'email' => $asset->uploader->email
                        ] : null
                    ];
                });

            return Inertia::render('Assets/Index', [
                'assets' => $assets
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in AssetController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman Assets.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // Max 100MB per file
        ]);

        $assets = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('assets');
            
            $asset = Asset::create([
                'name' => $file->getClientOriginalName(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'metadata' => [
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                ]
            ]);

            $assets[] = $asset;
        }

        return redirect()->back()->with('success', 'Asset berhasil diunggah.');
    }

    public function destroy(Asset $asset)
    {
        Storage::delete($asset->file_path);
        $asset->delete();

        return redirect()->back()->with('success', 'Asset berhasil dihapus.');
    }

    private function getAssetType($mimeType)
    {
        if (strpos($mimeType, 'image/') === 0) {
            return 'image';
        }
        if (strpos($mimeType, 'video/') === 0) {
            return 'video';
        }
        return 'document';
    }
} 