<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id', 'title', 'subtitle', 'note',
        'remind_at', 'completed', 'type', 'color', 'icon',
    ];
    
    // Map note to subtitle and vice versa
    protected $appends = ['time'];
    
    protected $casts = [
        'remind_at' => 'datetime',
        'completed' => 'boolean',
    ];
    
    public function getSubtitleAttribute(): string
    {
        return $this->attributes['subtitle'] ?? $this->attributes['note'] ?? '';
    }
    
    public function setSubtitleAttribute($value): void
    {
        $this->attributes['subtitle'] = $value;
        $this->attributes['note'] = $value;
    }
    
    public function getTimeAttribute(): ?string
    {
        return $this->remind_at ? $this->remind_at->format('M d, Y, h:i A') : null;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}