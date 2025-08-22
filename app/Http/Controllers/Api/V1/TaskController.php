<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SwaggerSchemas;
use App\Http\Requests\Api\V1\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="API Endpoints for managing tasks"
 * )
 */
class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        
        // Apply policy to all methods except index and show
        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getActionMethod(), ['index', 'show', 'projectTasks'])) {
                return $next($request);
            }
            
            if ($request->route()->getActionMethod() === 'updateStatus') {
                $task = Task::findOrFail($request->route('task'));
                if (!Gate::allows('changeStatus', $task)) {
                    return response()->json([
                        'message' => 'You are not authorized to perform this action.',
                    ], Response::HTTP_FORBIDDEN);
                }
                return $next($request);
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of the tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="Get all tasks",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tasks = $this->taskService->getUserTasks(auth()->user());
        
        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    /**
     * Get tasks for a specific project.
     *
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}/tasks",
     *     summary="Get all tasks for a project",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Project ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks for the project",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found"
     *     )
     * )
     */
    public function projectTasks(Project $project)
    {
        $this->authorize('view', $project);
        
        $tasks = $this->taskService->getProjectTasks($project->id, auth()->user());
        
        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created task in storage.
     *
     * @param TaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     )
     * )
     */
    public function store(TaskRequest $request)
    {
        $task = $this->taskService->createTask(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully.',
            'data' => $task,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{id}",
     *     summary="Get a specific task",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['project', 'assignee', 'creator']);
        
        return response()->json([
            'status' => 'success',
            'data' => $task,
        ]);
    }

    /**
     * Update the specified task in storage.
     *
     * @param TaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *     path="/api/v1/tasks/{id}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    /**
     * Update the status of the specified task.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Patch(
     *     path="/api/v1/tasks/{id}/status",
     *     summary="Update task status",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
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
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        
        $task = Task::findOrFail($id);
        
        // Check authorization
        if (!Gate::allows('changeStatus', $task)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this task status.',
            ], 403);
        }
        
        $task = $this->taskService->updateTaskStatus($task, $request->input('status'));

        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated successfully.',
            'data' => $task->load(['project', 'assignee', 'creator']),
        ]);
    }

    /**
     * Remove the specified task from storage.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/tasks/{id}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $this->taskService->deleteTask($task);

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully.',
        ]);
    }
}
