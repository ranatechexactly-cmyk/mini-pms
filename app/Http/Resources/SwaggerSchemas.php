<?php

namespace App\Http\Resources;

/**
 * @OA\Schema(
 *     schema="Project",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Project Title"),
 *     @OA\Property(property="description", type="string", example="Project Description"),
 *     @OA\Property(property="manager_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="manager", ref="#/components/schemas/User"),
 *     @OA\Property(property="developers", type="array", @OA\Items(ref="#/components/schemas/User"))
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", example="developer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Task Title"),
 *     @OA\Property(property="description", type="string", example="Task Description"),
 *     @OA\Property(property="project_id", type="integer", example=1),
 *     @OA\Property(property="assigned_to", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="priority", type="string", example="high"),
 *     @OA\Property(property="due_date", type="string", format="date"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="assignee", ref="#/components/schemas/User"),
 *     @OA\Property(property="project", ref="#/components/schemas/Project")
 * )
 * 
 * @OA\Schema(
 *     schema="ProjectRequest",
 *     type="object",
 *     required={"title"},
 *     @OA\Property(property="title", type="string", example="New Project"),
 *     @OA\Property(property="description", type="string", example="Project description")
 * )
 * 
 * @OA\Schema(
 *     schema="TaskRequest",
 *     type="object",
 *     required={"title", "project_id", "assigned_to"},
 *     @OA\Property(property="title", type="string", example="New Task"),
 *     @OA\Property(property="description", type="string", example="Task description"),
 *     @OA\Property(property="project_id", type="integer", example=1),
 *     @OA\Property(property="assigned_to", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="priority", type="string", example="high"),
 *     @OA\Property(property="due_date", type="string", format="date")
 * )
 */
class SwaggerSchemas
{
    // This class is used only for Swagger documentation
}
