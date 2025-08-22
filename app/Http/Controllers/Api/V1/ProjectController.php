<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectRequest;
use App\Models\Project;
use App\Models\User;
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
        $this->middleware('auth:sanctum');
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
        $this->authorize('create', Project::class);
        
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
    public function show($id)
    {
        // Explicitly find the project by ID
        $project = Project::find($id);
        
        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
                'data' => null
            ], 404);
        }
        
        $this->authorize('view', $project);
        
        // For developers, verify they are assigned to the project
        if (auth()->user()->isDeveloper() && !$project->developers->contains(auth()->id())) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view this project.',
                'data' => null
            ], 403);
        }
        
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
    public function destroy($id)
    {
        try {
            // Find the project including trashed ones
            $project = Project::withTrashed()->find($id);
            
            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project not found',
                    'data' => null
                ], 404);
            }
            
            // Check if already deleted
            if ($project->trashed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project has already been deleted',
                    'data' => null
                ], 410);
            }
            
            // Authorize the action using the ProjectPolicy
            $this->authorize('delete', $project);
            
            // Delete the project
            $project->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Project deleted successfully.',
                'data' => null
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete project. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
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
     * @param int $developerId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{project}/developers/{developerId}",
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
     *         name="developerId",
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
    public function removeDeveloper(Project $project, int $developerId)
    {
        // Authorize the action using the ProjectPolicy
        $this->authorize('removeDeveloper', $project);
        
        // Pass the developer ID directly
        $this->projectService->removeDeveloper($project, $developerId);

        return response()->json([
            'status' => 'success',
            'message' => 'Developer removed from project successfully.',
            'data' => null
        ]);
    }
}
