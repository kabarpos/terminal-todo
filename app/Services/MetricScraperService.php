<?php

namespace App\Services;

use App\Models\SocialAccount;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class MetricScraperService
{
    protected $browser;

    public function __construct()
    {
        $this->browser = new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]));
    }

    public function scrapeMetrics(SocialAccount $account)
    {
        return match ($account->platform->name) {
            'Instagram' => $this->scrapeInstagram($account),
            'Facebook' => $this->scrapeFacebook($account),
            'Twitter' => $this->scrapeTwitter($account),
            'TikTok' => $this->scrapeTikTok($account),
            default => throw new \Exception('Platform tidak didukung'),
        };
    }

    protected function scrapeInstagram(SocialAccount $account)
    {
        try {
            // Contoh untuk Instagram
            $username = $account->username;
            $crawler = $this->browser->request('GET', "https://www.instagram.com/{$username}/");
            
            // Cari data metrics dari script type="application/ld+json"
            $jsonData = $crawler->filter('script[type="application/ld+json"]')->text();
            $data = json_decode($jsonData, true);

            // Parse data yang dibutuhkan
            return [
                'followers_count' => $this->extractFollowersCount($crawler),
                'engagement_rate' => $this->calculateEngagementRate($crawler),
                'reach' => 0, // Data reach tidak tersedia publik
                'impressions' => 0, // Data impressions tidak tersedia publik
                'likes' => $this->extractLikes($crawler),
                'comments' => $this->extractComments($crawler),
                'shares' => 0, // Data shares tidak tersedia publik
                'date' => now()
            ];
        } catch (\Exception $e) {
            throw new \Exception("Gagal mengambil data Instagram: " . $e->getMessage());
        }
    }

    protected function scrapeFacebook(SocialAccount $account)
    {
        // Implementasi untuk Facebook
        throw new \Exception('Facebook scraping belum diimplementasikan');
    }

    protected function scrapeTwitter(SocialAccount $account)
    {
        // Implementasi untuk Twitter
        throw new \Exception('Twitter scraping belum diimplementasikan');
    }

    protected function scrapeTikTok(SocialAccount $account)
    {
        // Implementasi untuk TikTok
        throw new \Exception('TikTok scraping belum diimplementasikan');
    }

    protected function extractFollowersCount($crawler)
    {
        try {
            // Coba ekstrak dari meta tag
            $metaContent = $crawler->filter('meta[property="og:description"]')->attr('content');
            if (preg_match('/(\d+)\s+Followers/', $metaContent, $matches)) {
                return (int) str_replace(',', '', $matches[1]);
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function calculateEngagementRate($crawler)
    {
        // Hitung engagement rate berdasarkan likes dan comments dari beberapa post terakhir
        try {
            $totalEngagement = 0;
            $posts = $crawler->filter('article')->count();
            
            if ($posts > 0) {
                $followers = $this->extractFollowersCount($crawler);
                if ($followers > 0) {
                    return ($totalEngagement / ($posts * $followers)) * 100;
                }
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function extractLikes($crawler)
    {
        try {
            // Coba ekstrak jumlah likes dari post terbaru
            return 0; // Implementasi sesuai kebutuhan
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function extractComments($crawler)
    {
        try {
            // Coba ekstrak jumlah comments dari post terbaru
            return 0; // Implementasi sesuai kebutuhan
        } catch (\Exception $e) {
            return 0;
        }
    }
} 