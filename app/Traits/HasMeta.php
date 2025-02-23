<?php

namespace App\Traits;

use App\Models\UserMeta;
use Illuminate\Support\Collection;

trait HasMeta
{
    /**
     * Get all meta data
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * Get meta value by key
     */
    public function getMeta(string $key, $default = null)
    {
        $meta = $this->meta()->where('key', $key)->first();
        return $meta ? $meta->getMetaValue() : $default;
    }

    /**
     * Set meta value
     */
    public function setMeta(string $key, $value): void
    {
        $meta = $this->meta()->firstOrNew(['key' => $key]);
        $meta->setMetaValue($value);
        $this->meta()->save($meta);
    }

    /**
     * Set multiple meta values
     */
    public function setMetaMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->setMeta($key, $value);
        }
    }

    /**
     * Delete meta by key
     */
    public function deleteMeta(string $key): void
    {
        $this->meta()->where('key', $key)->delete();
    }

    /**
     * Get all meta as collection
     */
    public function getAllMeta(): Collection
    {
        return $this->meta->mapWithKeys(function ($meta) {
            return [$meta->key => $meta->getMetaValue()];
        });
    }

    /**
     * Check if meta exists
     */
    public function hasMeta(string $key): bool
    {
        return $this->meta()->where('key', $key)->exists();
    }

    /**
     * Sync meta data (delete missing keys)
     */
    public function syncMeta(array $values): void
    {
        // Delete keys not in the new values
        $this->meta()->whereNotIn('key', array_keys($values))->delete();
        
        // Update or create new values
        $this->setMetaMany($values);
    }
} 