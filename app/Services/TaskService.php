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
        if ($user->isManager()) {
            // Managers can see all tasks in their projects
            return Task::whereHas('project', function ($query) use ($user) {
                $query->where('manager_id', $user->id);
            })
            ->with(['project', 'assignee', 'creator'])
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
