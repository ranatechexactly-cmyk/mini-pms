<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tasks
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Admin can view any task
        if ($user->isAdmin()) {
            return true;
        }
        
        // Manager can view tasks in their projects
        if ($user->isManager() && $task->project && $task->project->manager_id === $user->id) {
            return true;
        }
        
        // Developers can only view their assigned tasks
        return $task->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // No one can create tasks (read-only)
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return false; // No one can update tasks (read-only)
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return false; // No one can delete tasks (read-only)
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine if the user can change the status of the task.
     */
    public function changeStatus(User $user, Task $task): bool
    {
        // Admin can change status of any task
        if ($user->isAdmin()) {
            return true;
        }
        
        // Assigned developer or project manager can change status
        return $task->assigned_to === $user->id || 
               ($user->isManager() && $task->project->manager_id === $user->id);
    }
}
