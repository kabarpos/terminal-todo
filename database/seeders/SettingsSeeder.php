<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'site_title',
                'value' => 'Nama Website',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Judul Website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Deskripsi website Anda',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Deskripsi Website'
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Logo Website'
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Favicon Website'
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2024 Website Anda. All rights reserved.',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Teks Footer'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
} 