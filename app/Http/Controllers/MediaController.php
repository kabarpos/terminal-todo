<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:view-media')->only('index');
        $this->middleware('permission:manage-media')->only(['store', 'destroy']);
    }

    public function index()
    {
        return Inertia::render('Media/Index', [
            'media' => Media::with('uploader')
                ->latest()
                ->paginate(12)
                ->through(fn ($media) => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'type' => $this->getMediaType($media->mime_type),
                    'size' => $media->file_size,
                    'url' => Storage::url($media->file_path),
                    'created_at' => $media->created_at->format('Y-m-d H:i:s'),
                    'uploader' => $media->uploader ? [
                        'name' => $media->uploader->name,
                        'avatar' => $media->uploader->avatar_url
                    ] : null,
                    'collection' => $media->collection,
                    'metadata' => $media->metadata
                ]),
            'can' => [
                'create' => auth()->user()->can('manage-media'),
                'delete' => auth()->user()->can('manage-media'),
            ],
            'filters' => [
                'type' => request('type', 'all'),
                'collection' => request('collection', 'all')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // Max 100MB
            'collection' => 'nullable|string|max:50'
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('media');
            
            $media = Media::create([
                'name' => $file->getClientOriginalName(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'collection' => $request->collection,
                'uploaded_by' => auth()->id(),
                'metadata' => [
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                    'dimensions' => $this->getImageDimensions($file),
                ]
            ]);

            $uploadedFiles[] = $media;
        }

        return back()->with('success', count($uploadedFiles) . ' file berhasil diunggah.');
    }

    public function destroy(Media $media)
    {
        Storage::delete($media->file_path);
        $media->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }

    private function getMediaType($mimeType)
    {
        $types = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'spreadsheet' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'presentation' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'video' => ['video/mp4', 'video/mpeg', 'video/quicktime'],
            'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
        ];

        foreach ($types as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $type;
            }
        }

        return 'other';
    }

    private function getImageDimensions($file)
    {
        if (strpos($file->getMimeType(), 'image/') === 0) {
            try {
                $image = getimagesize($file->path());
                return [
                    'width' => $image[0],
                    'height' => $image[1]
                ];
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
} 