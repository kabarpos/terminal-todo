<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class ArticleCrawlerService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function scrape($url)
    {
        try {
            $response = $this->client->get($url);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            // Default selectors untuk metadata umum
            $title = $this->extractContent($crawler, [
                'h1',
                'meta[property="og:title"]',
                'meta[name="twitter:title"]',
                'title'
            ]);

            $description = $this->extractContent($crawler, [
                'meta[name="description"]',
                'meta[property="og:description"]',
                'meta[name="twitter:description"]'
            ]);

            $image = $this->extractContent($crawler, [
                'meta[property="og:image"]',
                'meta[name="twitter:image"]',
                'link[rel="image_src"]'
            ]);

            // Mencoba mengekstrak konten artikel
            $content = $this->extractArticleContent($crawler);

            return [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'content' => $content,
                'url' => $url,
                'site_name' => parse_url($url, PHP_URL_HOST)
            ];
        } catch (Exception $e) {
            throw new Exception("Gagal mengambil konten dari URL: " . $e->getMessage());
        }
    }

    protected function extractContent($crawler, array $selectors)
    {
        foreach ($selectors as $selector) {
            try {
                $element = $crawler->filter($selector);
                if ($element->count() > 0) {
                    if ($element->first()->nodeName() === 'meta') {
                        return $element->first()->attr('content');
                    } else {
                        return trim($element->first()->text());
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }
        return null;
    }

    protected function extractArticleContent($crawler)
    {
        // Array dari selector yang umum digunakan untuk konten artikel
        $contentSelectors = [
            // Selector untuk detik.com
            '.detail__body-text',
            '.itp_bodycontent',
            '.detail__content',
            // Selector umum untuk artikel
            'article',
            '[class*="article-content"]',
            '[class*="post-content"]',
            '[class*="entry-content"]',
            '[class*="article__content"]',
            '[class*="article-body"]',
            '[class*="post-body"]',
            '[class*="story-content"]',
            '[class*="news-content"]',
            '.content',
            '#content',
            'main',
            // Selector spesifik
            '.body-text',
            '.article-text',
            '.news-text',
            '.story-body',
            // Fallback selectors
            'p:not(:empty)',
            '.text'
        ];

        foreach ($contentSelectors as $selector) {
            try {
                $content = $crawler->filter($selector);
                if ($content->count() > 0) {
                    // Kumpulkan semua teks dari paragraf
                    $paragraphs = [];
                    $content->filter('p')->each(function ($node) use (&$paragraphs) {
                        $text = trim($node->text());
                        if (!empty($text)) {
                            $paragraphs[] = $text;
                        }
                    });

                    // Jika tidak ada paragraf yang ditemukan, ambil semua teks
                    if (empty($paragraphs)) {
                        $html = $content->html();
                        // Hapus script dan style tags
                        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
                        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
                        // Bersihkan whitespace berlebih
                        $text = strip_tags($html);
                        $text = preg_replace('/\s+/', ' ', $text);
                        return trim($text);
                    }

                    // Gabungkan paragraf dengan baris baru
                    return implode("\n\n", $paragraphs);
                }
            } catch (Exception $e) {
                continue;
            }
        }
        return null;
    }
} 