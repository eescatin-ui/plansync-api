<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'tags',
        'course_id',
    ];
    
    protected $casts = [
        'tags' => 'array',
    ];
    
    // Add these to match React Native expectations
    protected $appends = ['createdAt', 'updatedAt'];
    
    public function getCreatedAtAttribute(): ?string
    {
        return $this->attributes['created_at'] ?? null;
    }
    
    public function getUpdatedAtAttribute(): ?string
    {
        return $this->attributes['updated_at'] ?? null;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}