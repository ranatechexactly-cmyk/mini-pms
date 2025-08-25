<?php

namespace App\Models;

use App\Events\TaskAssigned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'project_id',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'deadline',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        // Handle the created event
        static::created(function ($task) {
            if ($task->assigned_to) {
                $assignee = \App\Models\User::find($task->assigned_to);
                if ($assignee) {
                    event(new TaskAssigned($task, $assignee));
                }
            }
        });

        // Handle the updating event
        static::updating(function ($task) {
            if ($task->isDirty('assigned_to') && $task->assigned_to) {
                $originalAssignedTo = $task->getOriginal('assigned_to');
                
                if ($originalAssignedTo != $task->assigned_to) {
                    $assignee = \App\Models\User::find($task->assigned_to);
                    if ($assignee) {
                        event(new TaskAssigned($task, $assignee));
                    }
                }
            }
        });
    }
    
    /**
     * Get the project that owns the task.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Get the user that the task is assigned to.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Alias for assignedTo
     */
    public function assignee()
    {
        return $this->assignedTo();
    }
    
    /**
     * Get the user who created the task.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Alias for createdBy
     */
    public function creator()
    {
        return $this->createdBy();
    }
    
    /**
     * Determine if the user can view the task.
     */
    public function canView($user)
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isManager() && $this->project && $this->project->manager_id === $user->id) {
            return true;
        }
        
        if ($this->assigned_to === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Scope a query to only include tasks assigned to a specific user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include tasks in a specific project.
     */
    public function scopeInProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
