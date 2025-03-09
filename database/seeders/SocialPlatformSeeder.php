<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialPlatform;

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
                'icon' => 'fab fa-instagram',
                'description' => 'Platform berbagi foto dan video',
                'is_active' => true
            ],
            [
                'name' => 'Facebook',
                'icon' => 'fab fa-facebook',
                'description' => 'Platform jejaring sosial',
                'is_active' => true
            ],
            [
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'description' => 'Platform microblogging',
                'is_active' => true
            ],
            [
                'name' => 'TikTok',
                'icon' => 'fab fa-tiktok',
                'description' => 'Platform berbagi video pendek',
                'is_active' => true
            ],
            [
                'name' => 'YouTube',
                'icon' => 'fab fa-youtube',
                'description' => 'Platform berbagi video',
                'is_active' => true
            ],
            [
                'name' => 'LinkedIn',
                'icon' => 'fab fa-linkedin',
                'description' => 'Platform jejaring profesional',
                'is_active' => true
            ]
        ];

        foreach ($platforms as $platform) {
            SocialPlatform::create($platform);
        }
    }
}
