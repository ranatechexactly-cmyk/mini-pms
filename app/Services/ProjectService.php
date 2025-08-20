<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Get all projects for the authenticated user.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserProjects(User $user)
    {
        if ($user->isManager()) {
            return Project::with(['manager', 'developers'])->get();
        }

        return $user->projects()->with(['manager', 'developers'])->get();
    }

    /**
     * Create a new project.
     *
     * @param array $data
     * @param User $user
     * @return Project
     */
    public function createProject(array $data, User $user): Project
    {
        return DB::transaction(function () use ($data, $user) {
            $project = Project::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'manager_id' => $data['manager_id'] ?? $user->id,
            ]);

            // Assign developers if provided
            if (isset($data['developer_ids']) && is_array($data['developer_ids'])) {
                $project->developers()->sync($data['developer_ids']);
            }

            return $project->load(['manager', 'developers']);
        });
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'name' => $data['name'] ?? $project->name,
                'description' => $data['description'] ?? $project->description,
                'manager_id' => $data['manager_id'] ?? $project->manager_id,
            ]);

            // Update developers if provided
            if (isset($data['developer_ids']) && is_array($data['developer_ids'])) {
                $project->developers()->sync($data['developer_ids']);
            }

            return $project->load(['manager', 'developers']);
        });
    }

    /**
     * Delete a project.
     *
     * @param Project $project
     * @return bool|null
     */
    public function deleteProject(Project $project): ?bool
    {
        return $project->delete();
    }

    /**
     * Assign developers to a project.
     *
     * @param Project $project
     * @param array $developerIds
     * @return void
     */
    public function assignDevelopers(Project $project, array $developerIds): void
    {
        $project->developers()->sync($developerIds);
    }

    /**
     * Remove a developer from a project.
     *
     * @param Project $project
     * @param int $developerId
     * @return int
     */
    public function removeDeveloper(Project $project, int $developerId): int
    {
        return $project->developers()->detach($developerId);
    }
}
