<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Projects",
 *     description="API Endpoints for managing projects"
 * )
 */
class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
        
        // Apply policy to all methods except index and show
        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getActionMethod(), ['index', 'show'])) {
                return $next($request);
            }
            
            if (in_array($request->route()->getActionMethod(), ['assignDevelopers', 'removeDeveloper'])) {
                $project = Project::findOrFail($request->route('project'));
                if (!Gate::allows($request->route()->getActionMethod(), $project)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not authorized to perform this action.',
                        'data' => null
                    ], Response::HTTP_FORBIDDEN);
                }
                return $next($request);
            }
            
            return $next($request);
        })->except(['index', 'show']);
    }

    /**
     * Display a listing of the projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/projects",
     *     summary="Get all projects",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of projects",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function index()
    {
        $projects = $this->projectService->getUserProjects(auth()->user());
        
        return response()->json([
            'status' => 'success',
            'data' => $projects,
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param ProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/v1/projects",
     *     summary="Create a new project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="manager_id", type="integer")
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     )
     * )
     */
    public function store(ProjectRequest $request)
    {
        $project = $this->projectService->createProject(
            array_merge($request->validated(), [
                'developer_ids' => $request->input('developer_ids', [])
            ]),
            $request->user()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Project created successfully.',
            'data' => $project,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified project.
     *
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}",
     *     summary="Get a specific project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load(['manager', 'developers', 'tasks.assignee']);
        
        return response()->json([
            'status' => 'success',
            'data' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     *
     * @param ProjectRequest $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *     path="/api/v1/projects/{id}",
     *     summary="Update a project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="manager_id", type="integer")
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $project = $this->projectService->updateProject(
            $project,
            array_merge($request->validated(), [
                'developer_ids' => $request->input('developer_ids')
            ])
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully.',
            'data' => $project,
        ]);
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{id}",
     *     summary="Delete a project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Project deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        
        $this->projectService->deleteProject($project);

        return response()->json([
            'status' => 'success',
            'message' => 'Project deleted successfully.',
        ]);
    }

    /**
     * Assign developers to a project.
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/v1/projects/{id}/developers",
     *     summary="Assign developers to a project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"developers"},
     *             @OA\Property(property="developers", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Developers assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Developers assigned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function assignDevelopers(Request $request, Project $project)
    {
        $request->validate([
            'developer_ids' => 'required|array',
            'developer_ids.*' => 'exists:users,id',
        ]);

        $this->projectService->assignDevelopers($project, $request->input('developer_ids'));

        return response()->json([
            'status' => 'success',
            'message' => 'Developers assigned to project successfully.',
            'data' => $project->load('developers'),
        ]);
    }

    /**
     * Remove a developer from a project.
     *
     * @param Project $project
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{project}/developers/{user}",
     *     summary="Remove a developer from a project",
     *     tags={"Projects"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID of the developer to remove"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Developer removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Developer removed from project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project or user not found"
     *     )
     * )
     */
    public function removeDeveloper(Project $project, User $user)
    {
        $this->projectService->removeDeveloper($project, $user);

        return response()->json([
            'status' => 'success',
            'message' => 'Developer removed from project successfully.',
        ]);
    }
}
