<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaReport extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    protected $fillable = [
        'title',
        'category_id',
        'platform_id',
        'url',
        'description',
        'posting_date',
        'reach',
        'impressions',
        'engagement',
        'engagement_rate',
        'likes',
        'comments',
        'shares',
        'additional_metrics',
        'insights',
        'created_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'additional_metrics' => 'json',
        'engagement_rate' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 