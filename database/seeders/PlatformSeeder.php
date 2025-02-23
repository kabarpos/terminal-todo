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
                'description' => 'Platform media sosial untuk berbagi foto dan video',
                'settings' => json_encode([
                    'post_types' => ['image', 'video', 'carousel', 'reels', 'story'],
                    'image_dimensions' => [
                        'square' => '1080x1080',
                        'portrait' => '1080x1350',
                        'landscape' => '1080x608'
                    ],
                    'video_duration' => [
                        'feed' => '60',
                        'reels' => '90',
                        'story' => '15'
                    ]
                ])
            ],
            [
                'name' => 'TikTok',
                'icon' => 'tiktok',
                'description' => 'Platform media sosial untuk berbagi video pendek',
                'settings' => json_encode([
                    'video_dimensions' => [
                        'vertical' => '1080x1920'
                    ],
                    'max_duration' => '180'
                ])
            ],
            [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'description' => 'Platform berbagi video',
                'settings' => json_encode([
                    'post_types' => ['video', 'shorts'],
                    'video_dimensions' => [
                        'standard' => '1920x1080',
                        'shorts' => '1080x1920'
                    ],
                    'max_duration' => [
                        'video' => '43200', // 12 jam
                        'shorts' => '60'
                    ]
                ])
            ],
            [
                'name' => 'Facebook',
                'icon' => 'facebook',
                'description' => 'Platform media sosial untuk berbagi konten',
                'settings' => json_encode([
                    'post_types' => ['text', 'image', 'video', 'link'],
                    'image_dimensions' => [
                        'timeline' => '1200x630',
                        'story' => '1080x1920'
                    ],
                    'video_duration' => '14400' // 4 jam
                ])
            ],
            [
                'name' => 'Twitter/X',
                'icon' => 'twitter',
                'description' => 'Platform media sosial untuk berbagi tweet',
                'settings' => json_encode([
                    'post_types' => ['text', 'image', 'video', 'link'],
                    'character_limit' => '280',
                    'image_limit' => '4',
                    'video_duration' => '140'
                ])
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
