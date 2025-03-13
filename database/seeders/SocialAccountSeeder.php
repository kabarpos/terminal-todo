<?php

namespace Database\Seeders;

use App\Models\SocialAccount;
use App\Models\SocialPlatform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SocialAccountSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = SocialPlatform::all();

        foreach ($platforms as $platform) {
            // Cek apakah sudah ada akun untuk platform ini
            if (!SocialAccount::where('platform_id', $platform->id)->exists()) {
                SocialAccount::create([
                    'platform_id' => $platform->id,
                    'name' => 'Test Account ' . $platform->name,
                    'username' => 'test_' . strtolower($platform->name) . '_' . Str::random(8),
                    'profile_url' => 'https://' . strtolower($platform->name) . '.com/test_account',
                    'is_active' => true
                ]);
            }
        }
    }
} 