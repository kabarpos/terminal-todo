<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'platform_id',
        'created_by',
        'priority',
        'status',
        'start_date',
        'due_date',
        'completed_at',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relationships
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

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function approvals()
    {
        return $this->hasMany(TaskApproval::class);
    }

    public function dependencies()
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function dependentTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependent_task_id')
            ->withPivot('type', 'notes')
            ->withTimestamps();
    }

    public function workflow()
    {
        return $this->belongsToMany(Workflow::class, 'task_workflow')
            ->withPivot('current_step', 'step_history')
            ->withTimestamps();
    }

    public function contentBrief()
    {
        return $this->hasOne(ContentBrief::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TaskTimeLog::class);
    }
}
