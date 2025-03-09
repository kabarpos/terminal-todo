<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'metric_data';

    protected $fillable = [
        'social_account_id',
        'date',
        'followers_count',
        'engagement_rate',
        'reach',
        'impressions',
        'likes',
        'comments',
        'shares'
    ];

    protected $casts = [
        'date' => 'date',
        'followers_count' => 'integer',
        'engagement_rate' => 'float',
        'reach' => 'integer',
        'impressions' => 'integer',
        'likes' => 'integer',
        'comments' => 'integer',
        'shares' => 'integer'
    ];

    // Relasi dengan account
    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id')->withTrashed();
    }

    // Scope untuk filter berdasarkan periode
    public function scopeForPeriod($query, $year, $month = null, $week = null)
    {
        $query->where('year', $year);
        
        if ($month) {
            $query->where('month', $month);
        }
        
        if ($week) {
            $query->where('week_number', $week);
        }
        
        return $query;
    }

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }
} 