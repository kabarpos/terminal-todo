<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'subject_type',
        'subject_id'
    ];

    protected $casts = [
        'properties' => AsCollection::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering activities by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering activities by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get activity description with variables replaced
     */
    public function getFormattedDescription(): string
    {
        $description = $this->description;
        
        if ($this->properties) {
            foreach ($this->properties as $key => $value) {
                $description = str_replace(":$key", $value, $description);
            }
        }
        
        return $description;
    }
} 