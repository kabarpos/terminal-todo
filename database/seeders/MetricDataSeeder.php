<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Platform;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Log;

class MetricDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Truncate tables first
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('metric_data')->truncate();
            DB::table('social_accounts')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Mendapatkan platform Instagram dari tabel platforms
            $platform = Platform::where('name', 'Instagram')->first();
            
            if (!$platform) {
                $platform = Platform::create([
                    'name' => 'Instagram',
                    'slug' => 'instagram',
                    'icon' => 'instagram',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Create a test social account
            $account = SocialAccount::create([
                'name' => 'Test Instagram',
                'username' => '@test.instagram.' . rand(1000, 9999),
                'platform_id' => $platform->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Generate data for the last 30 days
            $startDate = Carbon::now()->subDays(30);
            $followers = 1000; // Starting followers count

            // Batch insert untuk performa lebih baik
            $metrics = [];
            
            for ($i = 0; $i < 30; $i++) { // Reduced to 30 days for faster seeding
                $date = $startDate->copy()->addDays($i);
                
                // Randomly increase followers (0-50 per day)
                $followers += rand(0, 50);
                
                // Generate random metrics
                $reach = rand(500, 2000);
                $impressions = $reach + rand(100, 500);
                $likes = rand(50, 200);
                $comments = rand(10, 50);
                $shares = rand(5, 25);
                
                // Calculate engagement rate
                $engagementRate = (($likes + $comments + $shares) / $impressions) * 100;

                $metrics[] = [
                    'social_account_id' => $account->id,
                    'date' => $date->format('Y-m-d'),
                    'followers_count' => $followers,
                    'engagement_rate' => round($engagementRate, 2),
                    'reach' => $reach,
                    'impressions' => $impressions,
                    'likes' => $likes,
                    'comments' => $comments,
                    'shares' => $shares,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insert metrics
            if (!empty($metrics)) {
                DB::table('metric_data')->insert($metrics);
            }

            // Generate more test accounts and their metrics
            $platformNames = [
                ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => 'facebook'],
                ['name' => 'Twitter', 'slug' => 'twitter', 'icon' => 'twitter'],
                ['name' => 'TikTok', 'slug' => 'tiktok', 'icon' => 'tiktok'],
                ['name' => 'YouTube', 'slug' => 'youtube', 'icon' => 'youtube']
            ];

            foreach ($platformNames as $platformData) {
                try {
                    // Cek apakah platform sudah ada
                    $platform = Platform::where('name', $platformData['name'])->first();
                    
                    if (!$platform) {
                        $platform = Platform::create([
                            'name' => $platformData['name'],
                            'slug' => $platformData['slug'],
                            'icon' => $platformData['icon'],
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $account = SocialAccount::create([
                        'name' => "Test {$platformData['name']}",
                        'username' => "@test.{$platformData['slug']}." . rand(1000, 9999),
                        'platform_id' => $platform->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $metrics = [];
                    $followers = rand(500, 5000); // Random starting followers

                    for ($i = 0; $i < 30; $i++) { // Reduced to 30 days
                        $date = $startDate->copy()->addDays($i);
                        
                        // Randomly increase followers
                        $followers += rand(0, 100);
                        
                        $reach = rand(1000, 5000);
                        $impressions = $reach + rand(200, 1000);
                        $likes = rand(100, 500);
                        $comments = rand(20, 100);
                        $shares = rand(10, 50);
                        
                        $engagementRate = (($likes + $comments + $shares) / $impressions) * 100;

                        $metrics[] = [
                            'social_account_id' => $account->id,
                            'date' => $date->format('Y-m-d'),
                            'followers_count' => $followers,
                            'engagement_rate' => round($engagementRate, 2),
                            'reach' => $reach,
                            'impressions' => $impressions,
                            'likes' => $likes,
                            'comments' => $comments,
                            'shares' => $shares,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    if (!empty($metrics)) {
                        DB::table('metric_data')->insert($metrics);
                    }
                } catch (\Exception $e) {
                    Log::error("Error creating metrics for {$platformData['name']}: " . $e->getMessage());
                    continue; // Skip ke platform berikutnya jika ada error
                }
            }
            
            Log::info('Metric data seeded successfully');
        } catch (\Exception $e) {
            Log::error('Error seeding metric data: ' . $e->getMessage());
        }
    }
} 