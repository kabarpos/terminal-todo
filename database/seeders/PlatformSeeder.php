<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $platforms = [
                [
                    'name' => 'Instagram',
                    'description' => 'Platform berbagi foto dan video',
                    'icon' => 'instagram',
                    'is_active' => true
                ],
                [
                    'name' => 'Facebook',
                    'description' => 'Platform jejaring sosial',
                    'icon' => 'facebook',
                    'is_active' => true
                ],
                [
                    'name' => 'Twitter',
                    'description' => 'Platform microblogging',
                    'icon' => 'twitter',
                    'is_active' => true
                ],
                [
                    'name' => 'TikTok',
                    'description' => 'Platform berbagi video pendek',
                    'icon' => 'tiktok',
                    'is_active' => true
                ],
                [
                    'name' => 'YouTube',
                    'description' => 'Platform berbagi video',
                    'icon' => 'youtube',
                    'is_active' => true
                ],
                [
                    'name' => 'LinkedIn',
                    'description' => 'Platform jejaring profesional',
                    'icon' => 'linkedin',
                    'is_active' => true
                ],
                [
                    'name' => 'Website',
                    'description' => 'Website resmi',
                    'icon' => 'globe',
                    'is_active' => true
                ]
            ];

            foreach ($platforms as $platformData) {
                try {
                    $slug = Str::slug($platformData['name']);
                    Platform::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $platformData['name'],
                            'description' => $platformData['description'],
                            'icon' => $platformData['icon'],
                            'is_active' => $platformData['is_active']
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("Error creating platform {$platformData['name']}: " . $e->getMessage());
                    continue;
                }
            }
            
            Log::info('Platforms seeded successfully');
        } catch (\Exception $e) {
            Log::error('Error seeding platforms: ' . $e->getMessage());
        }
    }
}
