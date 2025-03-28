<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Opsi 1: Gunakan updateOrCreate alih-alih firstOrCreate untuk menghindari duplikat
            // Opsi 2: Hapus data kategori yang sudah ada (gunakan salah satu)
            
            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            // DB::table('categories')->truncate();
            // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $categories = [
                // Content Categories
                [
                    'name' => 'Instagram Feed',
                    'type' => 'content',
                    'color' => '#E1306C',
                    'description' => 'Konten untuk feed Instagram'
                ],
                [
                    'name' => 'Instagram Story',
                    'type' => 'content',
                    'color' => '#833AB4',
                    'description' => 'Konten untuk Instagram Story'
                ],
                [
                    'name' => 'Instagram Reels',
                    'type' => 'content',
                    'color' => '#405DE6',
                    'description' => 'Konten video pendek untuk Instagram Reels'
                ],
                [
                    'name' => 'TikTok',
                    'type' => 'content',
                    'color' => '#000000',
                    'description' => 'Konten video untuk TikTok'
                ],
                [
                    'name' => 'YouTube',
                    'type' => 'content',
                    'color' => '#FF0000',
                    'description' => 'Konten video untuk YouTube'
                ],
                [
                    'name' => 'Facebook',
                    'type' => 'content',
                    'color' => '#1877F2',
                    'description' => 'Konten untuk Facebook'
                ],
                [
                    'name' => 'Twitter/X',
                    'type' => 'content',
                    'color' => '#1DA1F2',
                    'description' => 'Konten untuk Twitter/X'
                ],
                
                // Task Categories
                [
                    'name' => 'Content Writing',
                    'type' => 'task',
                    'color' => '#34A853',
                    'description' => 'Tugas pembuatan konten tulisan'
                ],
                [
                    'name' => 'Video Editing',
                    'type' => 'task',
                    'color' => '#EA4335',
                    'description' => 'Tugas editing video'
                ],
                [
                    'name' => 'Graphic Design',
                    'type' => 'task',
                    'color' => '#FBBC05',
                    'description' => 'Tugas desain grafis'
                ],
                [
                    'name' => 'Social Media',
                    'type' => 'task',
                    'color' => '#4285F4',
                    'description' => 'Tugas pengelolaan media sosial'
                ],
                [
                    'name' => 'Planning',
                    'type' => 'task',
                    'color' => '#9C27B0',
                    'description' => 'Tugas perencanaan konten'
                ],
                [
                    'name' => 'Review',
                    'type' => 'task',
                    'color' => '#FF9800',
                    'description' => 'Tugas review dan approval'
                ]
            ];

            foreach ($categories as $category) {
                $slug = Str::slug($category['name']);
                try {
                    Category::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $category['name'],
                            'type' => $category['type'],
                            'color' => $category['color'],
                            'description' => $category['description'],
                            'is_active' => true
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("Error creating category {$category['name']}: " . $e->getMessage());
                    continue; // Skip ke kategori berikutnya jika ada error
                }
            }
            
            Log::info('Categories seeded successfully');
        } catch (\Exception $e) {
            Log::error('Error seeding categories: ' . $e->getMessage());
        }
    }
}
