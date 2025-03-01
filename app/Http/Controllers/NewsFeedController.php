<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsFeed;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use App\Services\ArticleCrawlerService;

class NewsFeedController extends Controller
{
    use AuthorizesRequests;

    protected $crawler;

    public function __construct(ArticleCrawlerService $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feeds = NewsFeed::with(['user' => function($query) {
            $query->select('id', 'name', 'profile_photo_path');
        }])
        ->latest()
        ->paginate(10);

        return Inertia::render('NewsFeed/Index', [
            'feeds' => $feeds
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('NewsFeed/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:news,video,social_media,image',
            'url' => 'required_unless:type,image|url|nullable',
            'image' => 'required_if:type,image|image|max:5120|nullable', // max 5MB
            'title' => 'required_if:type,image|nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $data = [
                'user_id' => auth()->id(),
                'type' => $request->type
            ];

            if ($request->type === NewsFeed::TYPE_IMAGE) {
                // Handle uploaded image
                $data['title'] = $request->title;
                $data['description'] = $request->description;
                
                $newsFeed = NewsFeed::create($data);
                if ($request->hasFile('image')) {
                    $newsFeed->uploadImage($request->file('image'));
                }
            } else {
                // Handle URL-based content (news, video, social media)
                $metadata = NewsFeed::fetchMetaData($request->url, $request->type);
                
                if (!$metadata) {
                    return back()->withErrors(['url' => 'Tidak dapat mengambil metadata dari URL tersebut']);
                }

                $data = array_merge($data, [
                    'url' => $request->url,
                    'title' => $metadata['title'],
                    'description' => $metadata['description'],
                    'image_url' => $metadata['image_url'],
                    'video_url' => $metadata['video_url'] ?? null,
                    'site_name' => $metadata['site_name'],
                    'meta_data' => $metadata['meta_data']
                ]);

                $newsFeed = NewsFeed::create($data);
            }

            return redirect()->route('news-feeds.index')
                ->with('message', 'Feed berhasil ditambahkan!');

        } catch (\Exception $e) {
            \Log::error('Error creating news feed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan feed.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsFeed $newsFeed)
    {
        $newsFeed->load(['user' => function($query) {
            $query->select('id', 'name', 'profile_photo_path');
        }]);

        return Inertia::render('NewsFeed/Show', [
            'feed' => $newsFeed
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsFeed $newsFeed)
    {
        $this->authorize('update', $newsFeed);

        return Inertia::render('NewsFeed/Edit', [
            'feed' => $newsFeed
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsFeed $newsFeed)
    {
        $this->authorize('update', $newsFeed);

        $request->validate([
            'type' => 'required|in:news,video,social_media,image',
            'url' => 'required_unless:type,image|url|nullable',
            'image' => 'nullable|image|max:5120', // max 5MB
            'title' => 'required_if:type,image|nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            if ($request->type === NewsFeed::TYPE_IMAGE) {
                $data = [
                    'type' => $request->type,
                    'title' => $request->title,
                    'description' => $request->description
                ];

                if ($request->hasFile('image')) {
                    $newsFeed->uploadImage($request->file('image'));
                }

                $newsFeed->update($data);
            } else {
                // Jika URL berubah, update metadata
                if ($request->url !== $newsFeed->url) {
                    $metadata = NewsFeed::fetchMetaData($request->url, $request->type);
                    
                    if (!$metadata) {
                        return back()->withErrors(['url' => 'Tidak dapat mengambil metadata dari URL tersebut']);
                    }

                    $data = array_merge([
                        'type' => $request->type,
                        'url' => $request->url
                    ], $metadata);

                    $newsFeed->update($data);
                } else {
                    // Jika hanya tipe yang berubah
                    $newsFeed->update([
                        'type' => $request->type
                    ]);
                }
            }

            return redirect()->route('news-feeds.index')
                ->with('message', 'Feed berhasil diperbarui!');

        } catch (\Exception $e) {
            \Log::error('Error updating news feed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui feed.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsFeed $newsFeed)
    {
        $this->authorize('delete', $newsFeed);

        $newsFeed->delete();

        return redirect()->route('news-feeds.index')
            ->with('message', 'Feed berhasil dihapus!');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $metadata = NewsFeed::fetchMetaData($request->url);

        if (!$metadata) {
            return response()->json(['error' => 'Tidak dapat mengambil metadata dari URL tersebut'], 422);
        }

        return response()->json($metadata);
    }

    public function fetchMetadata(Request $request)
    {
        try {
            $url = $request->input('url');
            $metadata = $this->crawler->scrape($url);
            
            return response()->json([
                'success' => true,
                'data' => $metadata
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    protected function getDefaultProfilePhotoUrl($name)
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}
