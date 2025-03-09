<?php

namespace Database\Seeders;

use App\Models\MetricData;
use App\Models\SocialAccount;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MetricDataSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = SocialAccount::all();
        $date = Carbon::now()->subDays(30);

        foreach ($accounts as $account) {
            for ($i = 0; $i < 30; $i++) {
                $currentDate = $date->copy()->addDays($i);
                
                // Cek apakah data untuk kombinasi account_id dan date sudah ada
                if (!MetricData::where('social_account_id', $account->id)
                    ->whereDate('date', $currentDate)
                    ->exists()) {
                    
                    MetricData::create([
                        'social_account_id' => $account->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'followers_count' => rand(1000, 10000),
                        'engagement_rate' => rand(1, 100) / 10,
                        'reach' => rand(500, 5000),
                        'impressions' => rand(1000, 10000),
                        'likes' => rand(100, 1000),
                        'comments' => rand(10, 100),
                        'shares' => rand(5, 50)
                    ]);
                }
            }
        }
    }
} 