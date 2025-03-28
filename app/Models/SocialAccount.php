<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'platform_id',
        'name',
        'username',
        'url',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function metricData()
    {
        return $this->hasMany(MetricData::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
