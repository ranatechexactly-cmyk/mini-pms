<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin/Manager can view any project
        if ($user->isManager()) {
            return true;
        }
        
        // Developers can only view projects they're assigned to
        return $project->developers->contains('id', $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isManager() && $project->manager_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isManager() && $project->manager_id === $user->id;
    }

    /**
     * Determine whether the user can assign developers to the project.
     */
    public function assignDevelopers(User $user, Project $project): bool
    {
        return $user->isManager() && $project->manager_id === $user->id;
    }

    /**
     * Determine whether the user can remove developers from the project.
     */
    public function removeDeveloper(User $user, Project $project): bool
    {
        return $user->isManager() && $project->manager_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
