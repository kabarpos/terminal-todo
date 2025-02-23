<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'description',
        'is_active'
    ];

    protected $casts = [
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

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function editorialCalendars()
    {
        return $this->hasMany(EditorialCalendar::class);
    }
}
