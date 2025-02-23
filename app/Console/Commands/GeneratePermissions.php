<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionService;

class GeneratePermissions extends Command
{
    protected $signature = 'permissions:generate';
    protected $description = 'Generate permissions based on routes';

    public function handle(PermissionService $permissionService)
    {
        $this->info('Generating permissions...');
        
        $permissions = $permissionService->generatePermissions();
        
        $this->info('Generated ' . count($permissions) . ' permissions:');
        foreach ($permissions as $permission) {
            $this->line("- {$permission}");
        }
        
        $this->info('Done!');
    }
} 