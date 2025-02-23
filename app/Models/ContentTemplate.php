<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'platform_id',
        'category_id',
        'description',
        'structure',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'structure' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
