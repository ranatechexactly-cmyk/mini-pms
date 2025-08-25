<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class DashboardPolicy
{
    /**
     * Determine if the user can view projects.
     */
    public function viewProjects(User $user): bool
    {
        return true; // All authenticated users can view projects
    }

    /**
     * Determine if the user can create projects.
     */
    public function createProjects(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can view tasks.
     */
    public function viewTasks(User $user): bool
    {
        return true; // All authenticated users can view tasks
    }

    /**
     * Determine if the user can manage users.
     */
    public function manageUsers(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
