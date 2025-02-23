<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class GuardService
{
    /**
     * Register a new guard
     */
    public function registerGuard(string $name, array $config): void
    {
        // Add auth guard configuration
        Config::set('auth.guards.' . $name, array_merge([
            'driver' => 'session',
            'provider' => 'users',
        ], $config));
    }

    /**
     * Register a new guard provider
     */
    public function registerProvider(string $name, array $config): void
    {
        Config::set('auth.providers.' . $name, $config);
    }

    /**
     * Get all registered guards
     */
    public function getGuards(): array
    {
        return array_keys(Config::get('auth.guards', []));
    }

    /**
     * Get guard configuration
     */
    public function getGuardConfig(string $guard): ?array
    {
        return Config::get('auth.guards.' . $guard);
    }

    /**
     * Check if guard exists
     */
    public function guardExists(string $guard): bool
    {
        return array_key_exists($guard, Config::get('auth.guards', []));
    }

    /**
     * Get current guard
     */
    public function getCurrentGuard(): string
    {
        return Auth::getDefaultDriver();
    }

    /**
     * Set current guard
     */
    public function setCurrentGuard(string $guard): void
    {
        if ($this->guardExists($guard)) {
            Auth::shouldUse($guard);
        }
    }
} 