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
        // Create a test social account
        $accountId = DB::table('social_accounts')->insertGetId([
            'name' => 'Test Instagram',
            'platform' => 'instagram',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Generate data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $followers = 1000; // Starting followers count

        for ($i = 0; $i < 30; $i++) {
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

            DB::table('metric_data')->insert([
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
            ]);
        }
    }
} 