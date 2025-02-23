<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;
use Illuminate\Support\Str;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'Instagram',
                'icon' => 'instagram',
                'description' => 'Platform social media Instagram',
                'settings' => [
                    'post_types' => ['feed', 'story', 'reels'],
                    'image_dimensions' => [
                        'feed' => '1080x1080',
                        'story' => '1080x1920',
                        'reels' => '1080x1920'
                    ],
                    'video_duration' => [
                        'feed' => 60,
                        'story' => 15,
                        'reels' => 90
                    ]
                ]
            ],
            [
                'name' => 'TikTok',
                'icon' => 'tiktok',
                'description' => 'Platform video pendek TikTok',
                'settings' => [
                    'post_types' => ['video'],
                    'video_dimensions' => '1080x1920',
                    'max_duration' => 180
                ]
            ],
            [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'description' => 'Platform video YouTube',
                'settings' => [
                    'post_types' => ['video', 'shorts'],
                    'video_dimensions' => [
                        'landscape' => '1920x1080',
                        'shorts' => '1080x1920'
                    ],
                    'max_duration' => [
                        'video' => 43200, // 12 hours
                        'shorts' => 60
                    ]
                ]
            ],
            [
                'name' => 'Facebook',
                'icon' => 'facebook',
                'description' => 'Platform social media Facebook',
                'settings' => [
                    'post_types' => ['post', 'story', 'reels'],
                    'image_dimensions' => [
                        'post' => '1200x630',
                        'story' => '1080x1920'
                    ],
                    'video_duration' => [
                        'post' => 240,
                        'story' => 20,
                        'reels' => 90
                    ]
                ]
            ],
            [
                'name' => 'Twitter/X',
                'icon' => 'twitter',
                'description' => 'Platform microblogging Twitter/X',
                'settings' => [
                    'post_types' => ['tweet'],
                    'character_limit' => 280,
                    'image_limit' => 4,
                    'video_duration' => 140
                ]
            ]
        ];

        foreach ($platforms as $platform) {
            Platform::create([
                'name' => $platform['name'],
                'slug' => Str::slug($platform['name']),
                'icon' => $platform['icon'],
                'description' => $platform['description'],
                'settings' => $platform['settings'],
                'is_active' => true
            ]);
        }
    }
}
