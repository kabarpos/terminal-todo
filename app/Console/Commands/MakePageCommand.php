<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    protected $signature = 'make:page {name} {--layout=base}';
    protected $description = 'Create a new Inertia page with consistent layout';

    public function handle()
    {
        $name = $this->argument('name');
        $layout = $this->option('layout');
        
        // Generate path
        $path = resource_path("js/Pages/{$name}.vue");
        
        // Create directories if they don't exist
        File::makeDirectory(dirname($path), 0755, true, true);
        
        // Generate content
        $content = $this->generateTemplate($name, $layout);
        
        // Create file
        File::put($path, $content);
        
        // Create controller if it doesn't exist
        $this->createController($name);
        
        $this->info("Page created successfully: {$path}");
    }

    protected function generateTemplate(string $name, string $layout): string
    {
        $componentName = Str::studly($name);
        
        return <<<VUE
<script setup lang="ts">
import { ref } from 'vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { PageProps } from '@/types';

// Props definition
const props = defineProps<PageProps>();

// Component state
const loading = ref(false);

// Methods
const handleSubmit = async () => {
    loading.value = true;
    try {
        // Handle submission
    } catch (error) {
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <BaseLayout
        :auth="auth"
        title="{$componentName}"
        description="Description for {$componentName} page"
    >
        <template #actions>
            <!-- Add action buttons here -->
        </template>

        <!-- Main content -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Content Section
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    Add your content here.
                </p>
            </div>
        </div>
    </BaseLayout>
</template>
VUE;
    }

    protected function createController(string $name): void
    {
        $controllerName = Str::studly($name) . 'Controller';
        $this->call('make:controller', [
            'name' => $controllerName,
            '--invokable' => true,
        ]);
    }
} 