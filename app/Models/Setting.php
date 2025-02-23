<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    // Cache key
    private static $cacheKey = 'website_settings';

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value)
    {
        $setting = self::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        self::clearCache();
        return $setting;
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings()
    {
        return Cache::rememberForever(self::$cacheKey, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget(self::$cacheKey);
    }
} 