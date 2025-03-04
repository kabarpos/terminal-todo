<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'posting_date',
        'category_id',
        'url',
        'user_id',
    ];

    protected $casts = [
        'posting_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 