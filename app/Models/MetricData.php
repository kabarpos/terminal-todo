<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

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
        'shares' => 'integer',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($metric) {
            Log::info('MetricData deleting event triggered', [
                'id' => $metric->id,
                'exists' => $metric->exists,
                'is_force_deleting' => $metric->isForceDeleting(),
                'attributes' => $metric->getAttributes(),
                'deleted_at' => $metric->deleted_at,
                'user_id' => auth()->id(),
                'request_method' => request()->method(),
                'request_url' => request()->url()
            ]);
        });

        static::deleted(function ($metric) {
            Log::info('MetricData deleted event completed', [
                'id' => $metric->id,
                'exists' => $metric->exists,
                'trashed' => $metric->trashed(),
                'deleted_at' => $metric->deleted_at,
                'user_id' => auth()->id()
            ]);
        });
    }

    // Relasi dengan account
    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
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
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
} 