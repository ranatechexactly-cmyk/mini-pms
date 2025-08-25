<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Debug info
        \Log::info('Dashboard accessed', [
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'is_admin' => $user->isAdmin(),
            'is_manager' => $user->isManager(),
            'is_developer' => $user->isDeveloper()
        ]);
        
        // Initialize task query
        $taskQuery = Task::query();
        
        // Initialize project query based on user role
        if ($user->isAdmin()) {
            // Admins see all projects, including soft-deleted ones
            $projectQuery = \App\Models\Project::withTrashed();
            
            // Log the admin query for debugging
            \Log::info('Admin project query', [
                'user_id' => $user->id,
                'sql' => $projectQuery->toSql(),
                'bindings' => $projectQuery->getBindings()
            ]);
        } else {
            // For non-admin users, start with a base query
            $projectQuery = \App\Models\Project::query();
            
            if ($user->isManager()) {
                // Managers see projects they manage
                $projectQuery->where('manager_id', $user->id);
                
                // Get tasks from manager's projects
                $projectIds = $user->projects()->pluck('projects.id');
                $taskQuery->whereIn('project_id', $projectIds);
                
                \Log::info('Manager dashboard data', [
                    'user_id' => $user->id,
                    'managed_projects' => $projectIds,
                    'project_query' => $projectQuery->toSql(),
                    'task_query' => $taskQuery->toSql()
                ]);
                
            } elseif ($user->isDeveloper()) {
                // Developers see projects they're part of
                $projectIds = $user->projects()->pluck('projects.id');
                $projectQuery->whereIn('id', $projectIds);
                
                // Developers see only their assigned tasks
                $taskQuery->where('assigned_to', $user->id);
                
                \Log::info('Developer dashboard data', [
                    'user_id' => $user->id,
                    'assigned_projects' => $projectIds,
                    'project_query' => $projectQuery->toSql(),
                    'task_query' => $taskQuery->toSql()
                ]);
            }
        }
        
        // Get task counts
        $taskCounts = [
            'total' => (clone $taskQuery)->count(),
            'completed' => (clone $taskQuery)->where('status', 'completed')->count(),
            'in_progress' => (clone $taskQuery)->where('status', 'in_progress')->count(),
            'overdue' => (clone $taskQuery)
                ->where('status', '!=', 'completed')
                ->where('deadline', '<', now())
                ->count(),
        ];
        
        // Get project counts
        $projectCount = (clone $projectQuery)->count();
        
        // Debug logging
        \Log::info('Project Query Results', [
            'user_id' => $user->id,
            'is_admin' => $user->isAdmin(),
            'project_count' => $projectCount,
            'project_sql' => $projectQuery->toSql(),
            'project_bindings' => $projectQuery->getBindings()
        ]);
        
        $projectCounts = [
            'total' => $projectCount,
            'active' => $projectCount, // Same as total since we can't filter by status
            'completed' => 0, // No completed projects since there's no status column
        ];
        
        // Log the final counts
        \Log::info('Dashboard Counts', [
            'user_id' => $user->id,
            'project_counts' => $projectCounts,
            'task_counts' => $taskCounts
        ]);

        return view('dashboard', compact('taskCounts', 'projectCounts'));
    }
}
