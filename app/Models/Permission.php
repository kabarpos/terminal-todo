<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
        'guard_name',
    ];

    public function getNameAttribute($value)
    {
        return $this->attributes['slug'];
    }
} 