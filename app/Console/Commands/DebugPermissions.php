<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DebugPermissions extends Command
{
    protected $signature = 'debug:permissions {--user=} {--role=}';
    protected $description = 'Debug permissions for users and roles';

    public function handle()
    {
        // List semua permission
        $this->info('== SEMUA PERMISSION ==');
        $permissions = Permission::all();
        $this->table(['ID', 'Name', 'Guard'], $permissions->map(fn($p) => [$p->id, $p->name, $p->guard_name])->toArray());

        // List semua role dan permission-nya
        $this->info('== SEMUA ROLE DAN PERMISSION ==');
        $roles = Role::with('permissions')->get();
        foreach ($roles as $role) {
            $this->info("Role: {$role->name}");
            if ($role->permissions->count() > 0) {
                $this->table(
                    ['ID', 'Permission Name'], 
                    $role->permissions->map(fn($p) => [$p->id, $p->name])->toArray()
                );
            } else {
                $this->warn("  Role ini tidak memiliki permission");
            }
            $this->newLine();
        }

        // Cek user tertentu jika diminta
        if ($userId = $this->option('user')) {
            $user = User::find($userId);
            if ($user) {
                $this->info("== DETAIL USER: {$user->name} ({$user->email}) ==");
                
                // Role
                $this->info("User roles:");
                if ($user->roles->count() > 0) {
                    $this->table(
                        ['ID', 'Role Name'], 
                        $user->roles->map(fn($r) => [$r->id, $r->name])->toArray()
                    );
                } else {
                    $this->warn("  User tidak memiliki role");
                }

                // Direct Permissions
                $this->info("User direct permissions:");
                $directPermissions = $user->getDirectPermissions();
                if ($directPermissions->count() > 0) {
                    $this->table(
                        ['ID', 'Permission Name'], 
                        $directPermissions->map(fn($p) => [$p->id, $p->name])->toArray()
                    );
                } else {
                    $this->warn("  User tidak memiliki direct permission");
                }

                // All Permissions (including via roles)
                $this->info("User all permissions (including via roles):");
                $allPermissions = $user->getAllPermissions();
                if ($allPermissions->count() > 0) {
                    $this->table(
                        ['ID', 'Permission Name'], 
                        $allPermissions->map(fn($p) => [$p->id, $p->name])->toArray()
                    );
                } else {
                    $this->warn("  User tidak memiliki permission sama sekali");
                }
            } else {
                $this->error("User dengan ID {$userId} tidak ditemukan");
            }
        }

        // Cek role tertentu jika diminta
        if ($roleName = $this->option('role')) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $this->info("== DETAIL ROLE: {$role->name} ==");
                
                // Permissions
                $this->info("Role permissions:");
                if ($role->permissions->count() > 0) {
                    $this->table(
                        ['ID', 'Permission Name'], 
                        $role->permissions->map(fn($p) => [$p->id, $p->name])->toArray()
                    );
                } else {
                    $this->warn("  Role tidak memiliki permission");
                }

                // Users with this role
                $this->info("Users with this role:");
                $users = User::role($role->name)->get();
                if ($users->count() > 0) {
                    $this->table(
                        ['ID', 'Name', 'Email'], 
                        $users->map(fn($u) => [$u->id, $u->name, $u->email])->toArray()
                    );
                } else {
                    $this->warn("  Tidak ada user dengan role ini");
                }
            } else {
                $this->error("Role dengan nama '{$roleName}' tidak ditemukan");
            }
        }

        return 0;
    }
} 