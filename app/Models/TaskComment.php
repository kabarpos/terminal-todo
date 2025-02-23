<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'content',
        'parent_id',
        'attachments',
        'mentions'
    ];

    protected $casts = [
        'attachments' => 'array',
        'mentions' => 'array'
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(TaskComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(TaskComment::class, 'parent_id');
    }
} 