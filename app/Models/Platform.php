<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'settings',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function contentTemplates()
    {
        return $this->hasMany(ContentTemplate::class);
    }

    public function editorialCalendars()
    {
        return $this->hasMany(EditorialCalendar::class);
    }
}
