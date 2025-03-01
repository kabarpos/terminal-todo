<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Embed\Embed;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Services\ArticleCrawlerService;

class NewsFeed extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_NEWS = 'news';
    const TYPE_VIDEO = 'video';
    const TYPE_SOCIAL_MEDIA = 'social_media';
    const TYPE_IMAGE = 'image';

    protected $fillable = [
        'user_id',
        'type',
        'url',
        'title',
        'description',
        'image_url',
        'video_url',
        'site_name',
        'meta_data'
    ];

    protected $casts = [
        'meta_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function fetchMetaData($url, $type = null)
    {
        try {
            $crawler = new ArticleCrawlerService();
            $metadata = $crawler->scrape($url);

            return [
                'title' => $metadata['title'],
                'description' => $metadata['description'],
                'image_url' => $metadata['image'],
                'video_url' => null,
                'site_name' => $metadata['site_name'],
                'meta_data' => [
                    'content' => $metadata['content'],
                    'original_url' => $url
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching metadata: ' . $e->getMessage());
            return null;
        }
    }

    private static function extractVideoMetadata($xpath, $metadata)
    {
        // Coba ambil metadata video dari berbagai sumber
        $metadata['video_url'] = self::getMetaContent($xpath, 'meta[property="og:video:url"]');
        if (!$metadata['video_url']) {
            $metadata['video_url'] = self::getMetaContent($xpath, 'meta[property="og:video"]');
        }

        $metadata['title'] = self::getMetaContent($xpath, 'meta[property="og:title"]');
        $metadata['description'] = self::getMetaContent($xpath, 'meta[property="og:description"]');
        $metadata['image_url'] = self::getMetaContent($xpath, 'meta[property="og:image"]');
        $metadata['site_name'] = self::getMetaContent($xpath, 'meta[property="og:site_name"]');

        // Tambahan metadata khusus video
        $metadata['meta_data'] = [
            'duration' => self::getMetaContent($xpath, 'meta[property="video:duration"]'),
            'type' => self::getMetaContent($xpath, 'meta[property="og:video:type"]'),
        ];

        return $metadata;
    }

    private static function extractSocialMediaMetadata($xpath, $metadata)
    {
        $metadata['title'] = self::getMetaContent($xpath, 'meta[property="og:title"]');
        $metadata['description'] = self::getMetaContent($xpath, 'meta[property="og:description"]');
        $metadata['image_url'] = self::getMetaContent($xpath, 'meta[property="og:image"]');
        $metadata['site_name'] = self::getMetaContent($xpath, 'meta[property="og:site_name"]');

        // Tambahan metadata khusus social media
        $metadata['meta_data'] = [
            'author' => self::getMetaContent($xpath, 'meta[property="article:author"]'),
            'published_time' => self::getMetaContent($xpath, 'meta[property="article:published_time"]'),
        ];

        return $metadata;
    }

    private static function extractDefaultMetadata($xpath, $metadata)
    {
        // Default metadata untuk berita dan konten umum
        $metadata['title'] = self::getMetaContent($xpath, 'meta[property="og:title"]') ?: 
            self::getMetaContent($xpath, 'meta[name="title"]') ?: 
            self::getNodeContent($xpath, '//title');
            
        $metadata['description'] = self::getMetaContent($xpath, 'meta[property="og:description"]') ?: 
            self::getMetaContent($xpath, 'meta[name="description"]');
            
        $metadata['image_url'] = self::getMetaContent($xpath, 'meta[property="og:image"]') ?: 
            self::getMetaContent($xpath, 'meta[name="image"]');
            
        $metadata['site_name'] = self::getMetaContent($xpath, 'meta[property="og:site_name"]');

        // Tambahan metadata untuk berita
        $metadata['meta_data'] = [
            'author' => self::getMetaContent($xpath, 'meta[name="author"]'),
            'keywords' => self::getMetaContent($xpath, 'meta[name="keywords"]'),
            'published_date' => self::getMetaContent($xpath, 'meta[name="published_date"]'),
        ];

        return $metadata;
    }

    private static function getMetaContent($xpath, $query)
    {
        $meta = $xpath->query("//{$query}");
        return $meta->length > 0 ? $meta->item(0)->getAttribute('content') : null;
    }

    private static function getNodeContent($xpath, $query)
    {
        $node = $xpath->query($query);
        return $node->length > 0 ? $node->item(0)->nodeValue : null;
    }

    public function uploadImage($image)
    {
        if ($this->image_url) {
            // Hapus gambar lama jika ada
            Storage::disk('public')->delete($this->image_url);
        }

        $path = $image->store('news-feeds', 'public');
        $this->update(['image_url' => $path]);
    }
}
