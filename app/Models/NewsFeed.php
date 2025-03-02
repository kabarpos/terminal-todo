<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Embed\Embed;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Services\ArticleCrawlerService;
use App\Services\SocialMediaService;

class NewsFeed extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_NEWS = 'news';
    const TYPE_VIDEO = 'video';
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

    protected $appends = ['image_url_full'];

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
            if ($type === self::TYPE_IMAGE) {
                // Untuk tipe gambar yang diupload
                return [
                    'title' => '', // Akan diisi oleh user
                    'description' => '', // Akan diisi oleh user
                    'image_url' => null, // Akan diisi setelah upload
                    'video_url' => null,
                    'site_name' => 'Galeri Foto',
                    'meta_data' => [
                        'type' => 'image',
                        'original_filename' => null, // Akan diisi setelah upload
                        'file_size' => null, // Akan diisi setelah upload
                        'dimensions' => null, // Akan diisi setelah upload
                        'mime_type' => null, // Akan diisi setelah upload
                        'tags' => [], // Untuk kategorisasi gambar
                        'is_uploaded' => true
                    ]
                ];
            }

            if ($type === self::TYPE_VIDEO) {
                $videoId = self::extractYoutubeId($url);
                if (!$videoId) {
                    throw new Exception('URL video harus dari YouTube');
                }

                // Normalisasi URL YouTube ke format lengkap
                $normalizedUrl = "https://www.youtube.com/watch?v={$videoId}";

                // Ambil metadata dari halaman video dengan User-Agent
                $client = new Client();
                $response = $client->get($normalizedUrl, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ]
                ]);
                $html = $response->getBody()->getContents();

                // Ekstrak deskripsi dengan pola yang lebih lengkap
                $fullDescription = '';
                $descriptionPatterns = [
                    '/\"description\":\s*{\s*\"simpleText\":\s*\"(.*?)\"}/s',
                    '/\"description\":\s*\"(.*?)\"/',
                    '/\"shortDescription\":\s*\"(.*?)\"/',
                    '/meta property="og:description" content="([^"]+)"/',
                    '/meta name="description" content="([^"]+)"/'
                ];

                foreach ($descriptionPatterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        $fullDescription = $matches[1];
                        $fullDescription = stripcslashes($fullDescription);
                        $fullDescription = html_entity_decode($fullDescription, ENT_QUOTES | ENT_HTML5);
                        break;
                    }
                }

                // Ekstrak view count dengan pola yang lebih lengkap
                $viewCount = null;
                $viewCountPatterns = [
                    '/\"viewCount\":\s*\"([0-9,]+)\"/',
                    '/\"viewCount\":\s*([0-9]+)/',
                    '/\"viewCountText\":\s*{\s*\"simpleText\":\s*\"([^"]+)\"/',
                    '/\"viewCount\":\s*{\s*\"simpleText\":\s*\"([^"]+)\"/',
                    '/\"viewCount\":\s*{\s*\"text\":\s*\"([^"]+)\"/'
                ];

                foreach ($viewCountPatterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        $viewCount = $matches[1];
                        // Bersihkan format angka
                        $viewCount = preg_replace('/[^0-9]/', '', $viewCount);
                        break;
                    }
                }

                // Ekstrak tanggal publikasi
                $publishDate = null;
                $datePatterns = [
                    '/\"publishDate\":\s*\"([^"]+)\"/',
                    '/\"uploadDate\":\s*\"([^"]+)\"/',
                    '/meta itemprop="uploadDate" content="([^"]+)"/'
                ];

                foreach ($datePatterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        $publishDate = $matches[1];
                        break;
                    }
                }

                // Gunakan oEmbed untuk metadata dasar
                $oembedUrl = "https://www.youtube.com/oembed?url={$normalizedUrl}&format=json";
                $oembedResponse = $client->get($oembedUrl);
                $data = json_decode($oembedResponse->getBody(), true);

                \Log::info('YouTube metadata fetched', [
                    'video_id' => $videoId,
                    'normalized_url' => $normalizedUrl,
                    'view_count' => $viewCount,
                    'publish_date' => $publishDate,
                    'description_length' => strlen($fullDescription)
                ]);

                return [
                    'title' => $data['title'],
                    'description' => $fullDescription ?: $data['title'],
                    'image_url' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                    'video_url' => "https://www.youtube.com/embed/{$videoId}",
                    'site_name' => 'YouTube',
                    'meta_data' => [
                        'author_name' => $data['author_name'],
                        'author_url' => $data['author_url'],
                        'provider_name' => $data['provider_name'],
                        'type' => 'video',
                        'html' => $data['html'],
                        'video_id' => $videoId,
                        'publish_date' => $publishDate,
                        'view_count' => $viewCount,
                        'full_description' => $fullDescription
                    ]
                ];
            }

            // Default crawler untuk konten website
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
        } catch (Exception $e) {
            \Log::error('Error fetching metadata: ' . $e->getMessage(), [
                'url' => $url,
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return basic metadata jika gagal
            return [
                'title' => 'Untitled',
                'description' => '',
                'image_url' => null,
                'video_url' => null,
                'site_name' => 'Unknown',
                'meta_data' => [
                    'original_url' => $url
                ]
            ];
        }
    }

    private static function extractYoutubeId($url) 
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
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

    public function uploadImage($file)
    {
        try {
            // Hapus gambar lama jika ada
            if ($this->image_url) {
                Storage::disk('public')->delete($this->image_url);
            }

            // Generate nama file unik
            $filename = uniqid('img_') . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke storage
            $path = $file->storeAs('images/feeds', $filename, 'public');
            
            // Dapatkan dimensi gambar
            list($width, $height) = getimagesize($file->getRealPath());
            
            // Update metadata
            $this->update([
                'url' => null,
                'image_url' => $path,
                'meta_data' => [
                    'type' => 'image',
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'dimensions' => [
                        'width' => $width,
                        'height' => $height
                    ],
                    'mime_type' => $file->getMimeType(),
                    'is_uploaded' => true
                ]
            ]);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Error uploading image: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getImageUrlFullAttribute()
    {
        if (!$this->image_url) {
            return null;
        }
        return $this->image_url ? Storage::disk('public')->url($this->image_url) : null;
    }
}
