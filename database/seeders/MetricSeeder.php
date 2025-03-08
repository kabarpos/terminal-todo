<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Models\SocialPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = SocialPlatform::all();

        foreach ($platforms as $platform) {
            // Metrik umum untuk semua platform
            $commonMetrics = [
                [
                    'name' => 'Followers',
                    'key' => 'followers',
                    'unit' => 'number',
                    'description' => 'Jumlah pengikut',
                    'is_active' => true,
                ],
                [
                    'name' => 'Engagement Rate',
                    'key' => 'engagement_rate',
                    'unit' => 'percentage',
                    'description' => 'Tingkat keterlibatan audience',
                    'is_active' => true,
                ],
                [
                    'name' => 'Total Posts',
                    'key' => 'total_posts',
                    'unit' => 'number',
                    'description' => 'Jumlah postingan',
                    'is_active' => true,
                ],
            ];

            // Metrik spesifik per platform
            $platformSpecificMetrics = [
                'Instagram' => [
                    [
                        'name' => 'Story Views',
                        'key' => 'story_views',
                        'unit' => 'number',
                        'description' => 'Jumlah views story',
                    ],
                    [
                        'name' => 'Reach',
                        'key' => 'reach',
                        'unit' => 'number',
                        'description' => 'Jangkauan akun',
                    ],
                ],
                'YouTube' => [
                    [
                        'name' => 'Watch Time',
                        'key' => 'watch_time',
                        'unit' => 'minutes',
                        'description' => 'Total waktu tonton',
                    ],
                    [
                        'name' => 'Subscribers',
                        'key' => 'subscribers',
                        'unit' => 'number',
                        'description' => 'Jumlah subscriber',
                    ],
                ],
                'TikTok' => [
                    [
                        'name' => 'Video Views',
                        'key' => 'video_views',
                        'unit' => 'number',
                        'description' => 'Jumlah views video',
                    ],
                    [
                        'name' => 'Share Count',
                        'key' => 'shares',
                        'unit' => 'number',
                        'description' => 'Jumlah share',
                    ],
                ],
                'Facebook' => [
                    [
                        'name' => 'Page Likes',
                        'key' => 'page_likes',
                        'unit' => 'number',
                        'description' => 'Jumlah like halaman',
                    ],
                    [
                        'name' => 'Post Reach',
                        'key' => 'post_reach',
                        'unit' => 'number',
                        'description' => 'Jangkauan post',
                    ],
                ],
            ];

            // Tambahkan metrik umum
            foreach ($commonMetrics as $metric) {
                $metric['platform_id'] = $platform->id;
                Metric::create($metric);
            }

            // Tambahkan metrik spesifik platform
            if (isset($platformSpecificMetrics[$platform->name])) {
                foreach ($platformSpecificMetrics[$platform->name] as $metric) {
                    $metric['platform_id'] = $platform->id;
                    $metric['is_active'] = true;
                    Metric::create($metric);
                }
            }
        }
    }
}
