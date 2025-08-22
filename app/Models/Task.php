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

    /**
     * The event map for the model.
     *
     * @var array
     */
    // Remove the $dispatchesEvents property since we're handling events in booted()

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

    protected $casts = [
        'deadline' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'deadline',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeInProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
