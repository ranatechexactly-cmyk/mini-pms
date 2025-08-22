<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * Get all tasks for the authenticated user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTasks(User $user)
    {
        $query = Task::with(['project', 'assignee', 'creator']);

        if ($user->isAdmin()) {
            // Admins can see all tasks
            return $query->latest()->get();
        } elseif ($user->isManager()) {
            // Managers can see all tasks in their projects
            return $query->whereHas('project', function ($query) use ($user) {
                $query->where('manager_id', $user->id);
            })
            ->latest()
            ->get();
        }

        // Developers can only see their assigned tasks
        return $user->assignedTasks()
            ->with(['project', 'assignee', 'creator'])
            ->latest()
            ->get();
    }

    /**
     * Search and filter tasks based on criteria.
     *
     * @param User $user
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchTasks(User $user, array $filters = [])
    {
        try {
            // Start with a base query
            $query = Task::query()->with(['project', 'assignee', 'creator']);
            
            // Debug: Log the filters and user info
            \Log::info('Search Request:', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'filters' => $filters
            ]);

            // Apply search by title or description if provided
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply status filter if provided
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Apply priority filter if provided
            if (!empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            // Apply deadline range filter if provided
            if (!empty($filters['deadline_from'])) {
                $query->whereDate('deadline', '>=', $filters['deadline_from']);
            }
            if (!empty($filters['deadline_to'])) {
                $query->whereDate('deadline', '<=', $filters['deadline_to']);
            }

            // Log the query before role-based scoping
            \Log::debug('Query before role scoping:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

            // Apply user role-based scoping
            if ($user->isAdmin()) {
                // Admins can see all tasks
                $query->latest();
                \Log::debug('Applied Admin scope');
            } elseif ($user->isManager()) {
                // Managers can see all tasks in their projects
                $query->whereHas('project', function ($q) use ($user) {
                    $q->where('manager_id', $user->id);
                })->latest();
                \Log::debug('Applied Manager scope');
            } else {
                // Developers can only see their assigned tasks
                $query->where('assigned_to', $user->id)->latest();
                \Log::debug('Applied Developer scope', ['assigned_to' => $user->id]);
            }

            // Log the final query
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            \Log::debug('Final query:', ['sql' => $sql, 'bindings' => $bindings]);

            // Execute the query
            $tasks = $query->get();
            \Log::info('Search results:', [
                'task_count' => $tasks->count(),
                'task_ids' => $tasks->pluck('id')
            ]);

            // Return empty collection if no tasks found
            return $tasks;

        } catch (\Exception $e) {
            \Log::error('Search tasks error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get tasks for a specific project.
     *
     * @param int $projectId
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectTasks(int $projectId, User $user)
    {
        $query = Task::where('project_id', $projectId)
            ->with(['project', 'assignee', 'creator']);

        if (!$user->isManager()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->latest()->get();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @param User $user
     * @return Task
     */
    public function createTask(array $data, User $user): Task
    {
        return DB::transaction(function () use ($data, $user) {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'],
                'status' => $data['status'] ?? 'pending',
                'deadline' => $data['deadline'],
                'project_id' => $data['project_id'],
                'assigned_to' => $data['assigned_to'],
                'created_by' => $user->id,
            ]);

            return $task->load(['project', 'assignee', 'creator']);
        });
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'priority' => $data['priority'] ?? $task->priority,
            'deadline' => $data['deadline'] ?? $task->deadline,
            'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
        ]);

        return $task->load(['project', 'assignee', 'creator']);
    }

    /**
     * Update task status.
     *
     * @param Task $task
     * @param string $status
     * @return Task
     */
    public function updateTaskStatus(Task $task, string $status): Task
    {
        $task->update(['status' => $status]);
        return $task->load(['project', 'assignee', 'creator']);
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }
}
