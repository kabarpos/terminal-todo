<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialPlatform extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'api_key',
        'api_secret',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan social accounts
    public function accounts()
    {
        return $this->hasMany(SocialAccount::class, 'platform_id');
    }

    // Relasi dengan metrics
    public function metrics()
    {
        return $this->hasMany(Metric::class, 'platform_id');
    }

    // Scope untuk platform yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
