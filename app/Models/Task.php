<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids;
    
    protected $table = 'tasks';
    
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'completed',
    ];
    
    protected $casts = [
        'due_date' => 'datetime',
        'completed' => 'boolean',
    ];
    
    protected $appends = ['dueDate', 'status'];
    
    // Map React 'dueDate' to DB 'due_date'
 public function getDueDateAttribute(): ?string
{
    if (!isset($this->attributes['due_date']) || !$this->attributes['due_date']) {
        return null;
    }
    $date = $this->attributes['due_date'];
    return $date instanceof \DateTime 
        ? $date->format('Y-m-d')
        : date('Y-m-d', strtotime($date));
}
    // Map React 'status' to DB 'completed'
    public function getStatusAttribute(): string
    {
        return $this->completed ? 'done' : 'todo';
    }
    
    // Allow setting status (converts to completed boolean)
    public function setStatusAttribute($value): void
    {
        $this->attributes['completed'] = ($value === 'done');
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