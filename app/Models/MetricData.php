<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetricData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'metric_id',
        'value',
        'week_number',
        'month',
        'year',
        'recorded_at',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'metadata' => 'array',
        'recorded_at' => 'datetime',
        'week_number' => 'integer',
        'month' => 'integer',
        'year' => 'integer',
    ];

    // Relasi dengan account
    public function account()
    {
        return $this->belongsTo(SocialAccount::class, 'account_id');
    }

    // Relasi dengan metric
    public function metric()
    {
        return $this->belongsTo(Metric::class, 'metric_id');
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