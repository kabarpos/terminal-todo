<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SettingsVersionService
{
    protected string $backupPath = 'settings/backups';
    
    /**
     * Create a new settings version
     */
    public function createVersion(string $description = ''): array
    {
        $settings = Setting::all();
        $version = [
            'timestamp' => now()->toIso8601String(),
            'description' => $description,
            'settings' => $settings->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group
                ];
            })->toArray()
        ];

        // Store version file
        $filename = $this->generateVersionFilename();
        Storage::put(
            $this->backupPath . '/' . $filename,
            json_encode($version, JSON_PRETTY_PRINT)
        );

        return $version;
    }

    /**
     * Restore settings from a version
     */
    public function restoreVersion(string $versionFile): bool
    {
        if (!Storage::exists($this->backupPath . '/' . $versionFile)) {
            throw new \Exception('Version file not found');
        }

        $version = json_decode(
            Storage::get($this->backupPath . '/' . $versionFile),
            true
        );

        DB::beginTransaction();
        try {
            foreach ($version['settings'] as $setting) {
                $dbSetting = Setting::firstOrNew(['key' => $setting['key']]);
                $dbSetting->fill($setting);
                $dbSetting->save();
            }
            
            // Clear settings cache
            Cache::forget('website_settings');
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all available versions
     */
    public function getVersions(): array
    {
        $files = Storage::files($this->backupPath);
        $versions = [];

        foreach ($files as $file) {
            $content = json_decode(Storage::get($file), true);
            $versions[] = [
                'file' => basename($file),
                'timestamp' => $content['timestamp'],
                'description' => $content['description']
            ];
        }

        // Sort by timestamp descending
        usort($versions, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        return $versions;
    }

    /**
     * Create a backup of current settings
     */
    public function createBackup(string $description = 'Manual backup'): string
    {
        $version = $this->createVersion($description);
        return $this->generateVersionFilename();
    }

    /**
     * Restore from backup
     */
    public function restoreBackup(string $backupFile): bool
    {
        return $this->restoreVersion($backupFile);
    }

    /**
     * Clean old backups
     */
    public function cleanOldBackups(int $keepLast = 10): void
    {
        $versions = $this->getVersions();
        
        if (count($versions) <= $keepLast) {
            return;
        }

        $toDelete = array_slice($versions, $keepLast);
        foreach ($toDelete as $version) {
            Storage::delete($this->backupPath . '/' . $version['file']);
        }
    }

    /**
     * Generate version filename
     */
    protected function generateVersionFilename(): string
    {
        return 'settings_' . now()->format('Y-m-d_His') . '.json';
    }

    /**
     * Compare two versions
     */
    public function compareVersions(string $version1, string $version2): array
    {
        $content1 = json_decode(
            Storage::get($this->backupPath . '/' . $version1),
            true
        );
        $content2 = json_decode(
            Storage::get($this->backupPath . '/' . $version2),
            true
        );

        $differences = [];
        $settings1 = collect($content1['settings'])->keyBy('key');
        $settings2 = collect($content2['settings'])->keyBy('key');

        // Find changed and deleted settings
        foreach ($settings1 as $key => $setting) {
            if (!isset($settings2[$key])) {
                $differences[$key] = [
                    'type' => 'deleted',
                    'old' => $setting['value']
                ];
            } elseif ($setting['value'] !== $settings2[$key]['value']) {
                $differences[$key] = [
                    'type' => 'changed',
                    'old' => $setting['value'],
                    'new' => $settings2[$key]['value']
                ];
            }
        }

        // Find new settings
        foreach ($settings2 as $key => $setting) {
            if (!isset($settings1[$key])) {
                $differences[$key] = [
                    'type' => 'added',
                    'new' => $setting['value']
                ];
            }
        }

        return $differences;
    }
} 