<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'task_id',
        'category_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer'
    ];

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->hasMany(AssetTag::class);
    }
}
