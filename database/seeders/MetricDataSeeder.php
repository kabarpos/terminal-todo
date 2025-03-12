<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetricDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tables first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('metric_data')->truncate();
        DB::table('social_accounts')->truncate();
        DB::table('social_platforms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create a test platform first
        $platformId = DB::table('social_platforms')->insertGetId([
            'name' => 'Instagram',
            'icon' => 'instagram',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create a test social account
        $accountId = DB::table('social_accounts')->insertGetId([
            'name' => 'Test Instagram',
            'username' => '@test.instagram.' . rand(1000, 9999),
            'platform_id' => $platformId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Generate data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $followers = 1000; // Starting followers count

        // Batch insert untuk performa lebih baik
        $metrics = [];
        
        for ($i = 0; $i < 1000; $i++) { // Increased to 1000 days
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
                'social_account_id' => $accountId,
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

            // Insert in batches of 100
            if (count($metrics) >= 100) {
                DB::table('metric_data')->insert($metrics);
                $metrics = [];
            }
        }

        // Insert remaining metrics
        if (!empty($metrics)) {
            DB::table('metric_data')->insert($metrics);
        }

        // Generate more test accounts and their metrics
        $platforms = [
            ['name' => 'Facebook', 'icon' => 'facebook'],
            ['name' => 'Twitter', 'icon' => 'twitter'],
            ['name' => 'TikTok', 'icon' => 'tiktok'],
            ['name' => 'YouTube', 'icon' => 'youtube']
        ];

        foreach ($platforms as $platform) {
            $platformId = DB::table('social_platforms')->insertGetId([
                'name' => $platform['name'],
                'icon' => $platform['icon'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $accountId = DB::table('social_accounts')->insertGetId([
                'name' => "Test {$platform['name']}",
                'username' => "@test.{$platform['icon']}." . rand(1000, 9999),
                'platform_id' => $platformId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $metrics = [];
            $followers = rand(500, 5000); // Random starting followers

            for ($i = 0; $i < 1000; $i++) { // Increased to 1000 days
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
                    'social_account_id' => $accountId,
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

                if (count($metrics) >= 100) {
                    DB::table('metric_data')->insert($metrics);
                    $metrics = [];
                }
            }

            if (!empty($metrics)) {
                DB::table('metric_data')->insert($metrics);
            }
        }
    }
} 