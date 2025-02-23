<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class UserMeta extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'type'
    ];

    protected $casts = [
        'value' => AsCollection::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set meta value with appropriate type casting
     */
    public function setMetaValue($value)
    {
        $this->type = $this->detectValueType($value);
        $this->value = $value;
    }

    /**
     * Get meta value with type casting
     */
    public function getMetaValue()
    {
        return match($this->type) {
            'json' => json_decode($this->value, true),
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'array' => is_array($this->value) ? $this->value : json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Detect value type for proper storage
     */
    protected function detectValueType($value): string
    {
        return match(true) {
            is_array($value) => 'array',
            is_object($value) => 'json',
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            default => 'string',
        };
    }
} 