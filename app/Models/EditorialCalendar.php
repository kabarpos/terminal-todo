<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EditorialCalendar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'editorial_calendar';

    protected $fillable = [
        'title',
        'description',
        'platform_id',
        'category_id',
        'created_by',
        'publish_date',
        'deadline',
        'status',
        'metadata'
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'deadline' => 'datetime',
        'metadata' => 'array'
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'editorial_calendar_assignees', 'calendar_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(CalendarComment::class, 'calendar_id')
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
}
