<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $platforms = Platform::all();

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
                    'Twitter' => [
                        [
                            'name' => 'Retweets',
                            'key' => 'retweets',
                            'unit' => 'number',
                            'description' => 'Jumlah retweet',
                        ],
                        [
                            'name' => 'Likes',
                            'key' => 'likes',
                            'unit' => 'number',
                            'description' => 'Jumlah like',
                        ],
                    ],
                    'LinkedIn' => [
                        [
                            'name' => 'Impressions',
                            'key' => 'impressions',
                            'unit' => 'number',
                            'description' => 'Jumlah penayangan',
                        ],
                        [
                            'name' => 'Clicks',
                            'key' => 'clicks',
                            'unit' => 'number',
                            'description' => 'Jumlah klik',
                        ],
                    ],
                    'Website' => [
                        [
                            'name' => 'Views',
                            'key' => 'page_views',
                            'unit' => 'number',
                            'description' => 'Jumlah kunjungan',
                        ],
                        [
                            'name' => 'Bounce Rate',
                            'key' => 'bounce_rate',
                            'unit' => 'percentage',
                            'description' => 'Persentase pengunjung yang langsung pergi',
                        ],
                    ]
                ];

                // Tambahkan metrik umum
                foreach ($commonMetrics as $metric) {
                    try {
                        Metric::updateOrCreate(
                            [
                                'platform_id' => $platform->id,
                                'key' => $metric['key']
                            ],
                            [
                                'name' => $metric['name'],
                                'unit' => $metric['unit'],
                                'description' => $metric['description'],
                                'is_active' => $metric['is_active']
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error("Error creating common metric {$metric['name']} for platform {$platform->name}: " . $e->getMessage());
                        continue;
                    }
                }

                // Tambahkan metrik spesifik platform
                if (isset($platformSpecificMetrics[$platform->name])) {
                    foreach ($platformSpecificMetrics[$platform->name] as $metric) {
                        try {
                            Metric::updateOrCreate(
                                [
                                    'platform_id' => $platform->id,
                                    'key' => $metric['key']
                                ],
                                [
                                    'name' => $metric['name'],
                                    'unit' => $metric['unit'],
                                    'description' => $metric['description'],
                                    'is_active' => true
                                ]
                            );
                        } catch (\Exception $e) {
                            Log::error("Error creating specific metric {$metric['name']} for platform {$platform->name}: " . $e->getMessage());
                            continue;
                        }
                    }
                }
            }
            
            Log::info('Metrics seeded successfully');
        } catch (\Exception $e) {
            Log::error('Error seeding metrics: ' . $e->getMessage());
        }
    }
}
