<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metric extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'platform_id',
        'name',
        'key',
        'unit',
        'description',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    // Relasi dengan platform
    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    // Relasi dengan metric data
    public function metricData()
    {
        return $this->hasMany(MetricData::class, 'metric_id');
    }

    // Relasi dengan metric targets
    public function metricTargets()
    {
        return $this->hasMany(MetricTarget::class, 'metric_id');
    }

    // Scope untuk metrik yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
