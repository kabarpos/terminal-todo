<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetricTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'metric_id',
        'target_value',
        'period_start',
        'period_end',
        'notes',
        'is_achieved',
        'achieved_at',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'is_achieved' => 'boolean',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'achieved_at' => 'datetime',
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

    // Scope untuk target yang aktif
    public function scopeActive($query)
    {
        return $query->where('period_end', '>=', now());
    }

    // Scope untuk target yang tercapai
    public function scopeAchieved($query)
    {
        return $query->where('is_achieved', true);
    }

    // Scope untuk target yang belum tercapai
    public function scopeUnachieved($query)
    {
        return $query->where('is_achieved', false);
    }
} 