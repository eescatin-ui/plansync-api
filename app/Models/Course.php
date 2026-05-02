<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasUuids;
    
    protected $table = 'schedule_classes';
    
    protected $fillable = [
        'user_id', 'title', 'location', 'instructor',
        'day', 'start_time', 'end_time', 'color',
    ];
    
    // Add these to include in JSON
    protected $appends = ['name', 'time'];
    
    // Map 'name' to 'title'
    public function getNameAttribute(): string
    {
        return $this->title;
    }
    
    // Map 'time' to combine start_time and end_time
    public function getTimeAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
    
    public function user() { return $this->belongsTo(User::class); }
}