<?php

namespace Database\Seeders;

use App\Models\SocialPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialPlatformSeeder extends Seeder
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
                'description' => 'Platform berbagi foto dan video',
                'is_active' => true,
            ],
            [
                'name' => 'Facebook',
                'icon' => 'facebook',
                'description' => 'Platform jejaring sosial terbesar',
                'is_active' => true,
            ],
            [
                'name' => 'X (Twitter)',
                'icon' => 'twitter',
                'description' => 'Platform microblogging',
                'is_active' => true,
            ],
            [
                'name' => 'TikTok',
                'icon' => 'tiktok',
                'description' => 'Platform berbagi video pendek',
                'is_active' => true,
            ],
            [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'description' => 'Platform berbagi video',
                'is_active' => true,
            ],
            [
                'name' => 'LinkedIn',
                'icon' => 'linkedin',
                'description' => 'Platform jejaring profesional',
                'is_active' => true,
            ],
            [
                'name' => 'Threads',
                'icon' => 'threads',
                'description' => 'Platform microblogging dari Meta',
                'is_active' => true,
            ],
        ];

        foreach ($platforms as $platform) {
            SocialPlatform::create($platform);
        }
    }
}
