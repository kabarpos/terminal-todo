<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'calendar_id',
        'user_id',
        'content',
        'link_url',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'attachment_size'
    ];

    // Relationships
    public function calendar()
    {
        return $this->belongsTo(EditorialCalendar::class, 'calendar_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 