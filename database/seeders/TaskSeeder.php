<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Platform;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada minimal 1 user, category, dan platform
        $user = User::first() ?? User::factory()->create();
        $category = Category::first() ?? Category::create([
            'name' => 'Development',
            'type' => 'task',
            'color' => '#3B82F6'
        ]);
        $platform = Platform::first() ?? Platform::create([
            'name' => 'Website',
            'icon' => 'chrome'
        ]);

        // Data tasks untuk testing
        $tasks = [
            [
                'title' => 'Implementasi Fitur Login',
                'description' => 'Membuat sistem autentikasi dengan Laravel Breeze dan Vue',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Design Database Schema',
                'description' => 'Merancang struktur database untuk modul task management',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Setup CI/CD Pipeline',
                'description' => 'Konfigurasi GitHub Actions untuk automated testing dan deployment',
                'priority' => 'medium',
                'status' => 'draft',
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Optimasi Query',
                'description' => 'Mengoptimasi query N+1 problem di halaman tasks',
                'priority' => 'low',
                'status' => 'draft',
                'due_date' => now()->addDays(10),
            ],
            [
                'title' => 'Unit Testing',
                'description' => 'Menulis unit test untuk TaskController',
                'priority' => 'medium',
                'status' => 'draft',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Bug Fixing: Dark Mode',
                'description' => 'Memperbaiki tampilan komponen di dark mode',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => now()->addDay(),
            ],
            [
                'title' => 'Dokumentasi API',
                'description' => 'Membuat dokumentasi API menggunakan Swagger',
                'priority' => 'low',
                'status' => 'draft',
                'due_date' => now()->addDays(14),
            ],
            [
                'title' => 'Security Review',
                'description' => 'Review keamanan aplikasi dan implementasi best practices',
                'priority' => 'urgent',
                'status' => 'draft',
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Performance Testing',
                'description' => 'Melakukan load testing menggunakan JMeter',
                'priority' => 'medium',
                'status' => 'draft',
                'due_date' => now()->addDays(6),
            ],
            [
                'title' => 'Refactor Components',
                'description' => 'Refactor komponen Vue untuk meningkatkan reusability',
                'priority' => 'high',
                'status' => 'cancelled',
                'due_date' => now()->addDays(4),
            ]
        ];

        // Create tasks
        foreach ($tasks as $taskData) {
            $task = Task::create([
                ...$taskData,
                'category_id' => $category->id,
                'platform_id' => $platform->id,
                'created_by' => $user->id,
            ]);

            // Assign task to creator
            $task->assignees()->attach($user->id);
        }
    }
} 